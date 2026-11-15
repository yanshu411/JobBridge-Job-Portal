<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_role('employer');
if (!is_post()) { http_response_code(405); exit('Method not allowed.'); }
verify_csrf();
$id = filter_var($_POST['application_id'] ?? null, FILTER_VALIDATE_INT);
$status = (string)($_POST['status'] ?? '');
if (!$id || !in_array($status, ['submitted','reviewed','shortlisted','rejected'], true)) {
    http_response_code(400); exit('Invalid application update.');
}
$stmt = $pdo->prepare('SELECT a.job_id FROM applications a JOIN jobs j ON j.id=a.job_id WHERE a.id=? AND j.employer_id=?');
$stmt->execute([$id, $user['id']]);
$application = $stmt->fetch();
if (!$application) { http_response_code(404); exit('Application not found.'); }
$stmt = $pdo->prepare('UPDATE applications SET status=? WHERE id=?');
$stmt->execute([$status, $id]);
flash('success', 'Applicant status updated.');
redirect('applicants.php?job_id=' . (int)$application['job_id']);

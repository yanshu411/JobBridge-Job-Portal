<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_role('employer');
if (!is_post()) { http_response_code(405); exit('Method not allowed.'); }
verify_csrf();
$id = filter_var($_POST['job_id'] ?? null, FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(400); exit('Invalid job.'); }
$stmt = $pdo->prepare("UPDATE jobs SET status='closed' WHERE id=? AND employer_id=?");
$stmt->execute([$id, $user['id']]);
flash($stmt->rowCount() ? 'success' : 'info', $stmt->rowCount() ? 'Job listing closed.' : 'No listing changed.');
redirect('manage_jobs.php');

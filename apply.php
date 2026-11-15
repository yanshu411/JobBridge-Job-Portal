<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_role('candidate');
if (!is_post()) { http_response_code(405); exit('Method not allowed.'); }
verify_csrf();
$jobId = filter_var($_POST['job_id'] ?? null, FILTER_VALIDATE_INT);
$cover = trim((string)($_POST['cover_letter'] ?? ''));
if (!$jobId || $jobId < 1) { http_response_code(400); exit('Invalid job.'); }
if (char_length($cover) > 2000) {
    flash('error', 'Cover note must be under 2,000 characters.');
    redirect('job.php?id=' . $jobId);
}
$stmt = $pdo->prepare("SELECT id FROM jobs WHERE id=? AND status='open'");
$stmt->execute([$jobId]);
if (!$stmt->fetch()) { flash('error', 'This listing is not accepting applications.'); redirect('jobs.php'); }
try {
    $stmt = $pdo->prepare('INSERT INTO applications(job_id,candidate_id,cover_letter) VALUES(?,?,?)');
    $stmt->execute([$jobId, $user['id'], $cover]);
    flash('success', 'Application submitted successfully!');
    redirect('my_applications.php');
} catch (PDOException $e) {
    if ($e->getCode() === '23000') {
        flash('info', 'You have already applied to this job.');
        redirect('job.php?id=' . $jobId);
    }
    throw $e;
}

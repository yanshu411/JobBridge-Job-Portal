<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_role('employer');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(404); exit('Listing not found.'); }
$stmt = $pdo->prepare('SELECT * FROM jobs WHERE id=? AND employer_id=?');
$stmt->execute([$id, $user['id']]);
$job = $stmt->fetch();
if (!$job) { http_response_code(404); exit('Listing not found.'); }
$errors = [];
$form = $job;
if (is_post()) {
    verify_csrf();
    [$data, $errors] = validate_job($_POST);
    $status = (string)($_POST['status'] ?? '');
    if (!in_array($status, ['open','closed'], true)) $errors[] = 'Choose a valid listing status.';
    $form = array_merge($_POST, $data);
    if (!$errors) {
        $stmt = $pdo->prepare('UPDATE jobs SET title=?,company=?,location=?,category=?,job_type=?,work_mode=?,salary_min=?,salary_max=?,description=?,requirements=?,status=? WHERE id=? AND employer_id=?');
        $stmt->execute([$data['title'],$data['company'],$data['location'],$data['category'],$data['job_type'],$data['work_mode'],$data['salary_min'],$data['salary_max'],$data['description'],$data['requirements'],$status,$id,$user['id']]);
        flash('success', 'Job listing updated.');
        redirect('manage_jobs.php');
    }
}
$page_title = 'Edit job listing';
$button_label = 'Save changes';
require __DIR__ . '/includes/header.php';
?>
<div class="container"><div class="page-heading"><div class="eyebrow">Employer workspace</div><h1>Edit job listing</h1><p class="muted">Update the details or reopen a closed position.</p></div>
  <?php if ($errors): ?><div class="error-list"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <?php require __DIR__ . '/includes/job_form.php'; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

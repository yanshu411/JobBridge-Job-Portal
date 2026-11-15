<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_role('employer');
$page_title = 'Post a new job';
$errors = [];
$form = $_POST;
if (is_post()) {
    verify_csrf();
    [$data, $errors] = validate_job($_POST);
    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO jobs (employer_id,title,company,location,category,job_type,work_mode,salary_min,salary_max,description,requirements) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([$user['id'],$data['title'],$data['company'],$data['location'],$data['category'],$data['job_type'],$data['work_mode'],$data['salary_min'],$data['salary_max'],$data['description'],$data['requirements']]);
        flash('success', 'Your job listing has been published!');
        redirect('manage_jobs.php');
    }
}
$button_label = 'Publish job →';
require __DIR__ . '/includes/header.php';
?>
<div class="container"><div class="page-heading"><div class="eyebrow">Employer workspace</div><h1>Post a new job</h1><p class="muted">Write a clear listing to attract the right candidates.</p></div>
  <?php if ($errors): ?><div class="error-list"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <?php require __DIR__ . '/includes/job_form.php'; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

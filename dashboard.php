<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_login();
$page_title = 'Your dashboard';
if ($user['role'] === 'employer') {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM jobs WHERE employer_id=?');
    $stmt->execute([$user['id']]);
    $total = (int)$stmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE employer_id=? AND status='open'");
    $stmt->execute([$user['id']]);
    $active = (int)$stmt->fetchColumn();
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM applications a JOIN jobs j ON j.id=a.job_id WHERE j.employer_id=?');
    $stmt->execute([$user['id']]);
    $applications = (int)$stmt->fetchColumn();
    $stmt = $pdo->prepare('SELECT j.id,j.title,j.company,j.status,j.created_at,(SELECT COUNT(*) FROM applications a WHERE a.job_id=j.id) AS application_count FROM jobs j WHERE j.employer_id=? ORDER BY j.created_at DESC,j.id DESC LIMIT 5');
    $stmt->execute([$user['id']]);
    $recent = $stmt->fetchAll();
} else {
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM applications WHERE candidate_id=?');
    $stmt->execute([$user['id']]);
    $total = (int)$stmt->fetchColumn();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM applications WHERE candidate_id=? AND status='shortlisted'");
    $stmt->execute([$user['id']]);
    $active = (int)$stmt->fetchColumn();
    $applications = (int)$pdo->query("SELECT COUNT(*) FROM jobs WHERE status='open'")->fetchColumn();
    $stmt = $pdo->prepare('SELECT a.status,a.created_at,j.title,j.company,j.id AS job_id FROM applications a JOIN jobs j ON j.id=a.job_id WHERE a.candidate_id=? ORDER BY a.created_at DESC,a.id DESC LIMIT 5');
    $stmt->execute([$user['id']]);
    $recent = $stmt->fetchAll();
}
require __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="page-heading"><div class="eyebrow"><?= $user['role'] === 'employer' ? 'Employer workspace' : 'Candidate workspace' ?></div><h1>Welcome back, <?= e($user['full_name']) ?>!</h1><p class="muted">Here's what's happening with your <?= $user['role'] === 'employer' ? 'job listings' : 'job search' ?>.</p></div>
  <div class="stat-grid">
    <div class="surface stat-card"><div class="label"><?= $user['role'] === 'employer' ? 'Total listings' : 'Applications sent' ?></div><div class="value"><?= $total ?></div><div class="sub">All time</div></div>
    <div class="surface stat-card"><div class="label"><?= $user['role'] === 'employer' ? 'Open listings' : 'Shortlisted' ?></div><div class="value"><?= $active ?></div><div class="sub"><?= $user['role'] === 'employer' ? 'Accepting applications' : 'Applications shortlisted' ?></div></div>
    <div class="surface stat-card"><div class="label"><?= $user['role'] === 'employer' ? 'Applications received' : 'Open jobs' ?></div><div class="value"><?= $applications ?></div><div class="sub"><?= $user['role'] === 'employer' ? 'Across all your listings' : 'Explore your options' ?></div></div>
  </div>
  <div class="surface padded">
    <div class="section-caption"><h2><?= $user['role'] === 'employer' ? 'Recent listings' : 'Recent applications' ?></h2><a class="link small" href="<?= $user['role'] === 'employer' ? 'manage_jobs.php' : 'my_applications.php' ?>">See all →</a></div>
    <?php if ($recent): ?>
      <?php foreach ($recent as $item): ?>
        <div class="list-item"><div><h3><a href="job.php?id=<?= (int)($item['job_id'] ?? $item['id']) ?>"><?= e($item['title']) ?></a></h3><p><?= e($item['company']) ?> · <?= e(age_label($item['created_at'])) ?></p></div><span class="badge <?= $item['status'] === 'closed' ? 'gray' : '' ?>"><?= e(ucfirst($item['status'])) ?></span></div>
      <?php endforeach; ?>
    <?php else: ?><div class="empty"><div class="empty-icon">◫</div><h3>Nothing here yet</h3><p><?= $user['role'] === 'employer' ? 'Your new job postings will appear here.' : 'Apply to a job and track it from here.' ?></p></div><?php endif; ?>
  </div>
  <div class="action-row" style="margin-top:22px">
    <?php if ($user['role'] === 'employer'): ?><a class="btn btn-primary" href="post_job.php">+ Post a new job</a><a class="btn btn-light" href="manage_jobs.php">Manage listings</a>
    <?php else: ?><a class="btn btn-primary" href="jobs.php">Explore jobs →</a><a class="btn btn-light" href="my_applications.php">Track applications</a><?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

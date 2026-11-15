<?php
require __DIR__ . '/includes/bootstrap.php';
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(404); exit('Job not found.'); }
$stmt = $pdo->prepare('SELECT j.*, u.full_name AS posted_by FROM jobs j JOIN users u ON u.id = j.employer_id WHERE j.id = ?');
$stmt->execute([$id]);
$job = $stmt->fetch();
if (!$job) { http_response_code(404); exit('Job not found.'); }
$user = current_user();
$hasApplied = false;
if ($user && $user['role'] === 'candidate') {
    $stmt = $pdo->prepare('SELECT id FROM applications WHERE job_id = ? AND candidate_id = ?');
    $stmt->execute([$job['id'], $user['id']]);
    $hasApplied = (bool)$stmt->fetch();
}
$page_title = $job['title'] . ' at ' . $job['company'];
require __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="page-heading"><a class="link small" href="jobs.php">← Back to job listings</a></div>
  <div class="content-grid">
    <div class="content-grid-main">
      <section class="surface padded detail-section">
        <div class="detail-heading"><div class="company-icon"><?= e(strtoupper(substr($job['company'], 0, 1))) ?></div><div><h1><?= e($job['title']) ?></h1><p><?= e($job['company']) ?> · <?= e($job['location']) ?></p><div class="tags" style="margin-top:12px"><span class="badge <?= $job['status'] === 'open' ? 'green' : 'gray' ?>"><?= $job['status'] === 'open' ? 'Actively hiring' : 'Closed' ?></span><span class="tag"><?= e($job['job_type']) ?></span><span class="tag"><?= e($job['work_mode']) ?></span></div></div></div>
        <hr style="border:none;border-top:1px solid #e8edf4;margin:20px 0">
        <h2>About this role</h2><div class="detail-text"><?= e($job['description']) ?></div>
        <?php if (trim((string)$job['requirements']) !== ''): ?><h2 style="margin-top:30px">Skills & requirements</h2><div class="detail-text"><?= e($job['requirements']) ?></div><?php endif; ?>
      </section>
    </div>
    <aside class="surface padded">
      <h3 style="margin-bottom:16px">Job overview</h3>
      <div class="side-stat"><span>Salary</span><strong><?= e(money_range($job['salary_min'], $job['salary_max'])) ?></strong></div>
      <div class="side-stat"><span>Category</span><strong><?= e($job['category']) ?></strong></div>
      <div class="side-stat"><span>Employment</span><strong><?= e($job['job_type']) ?></strong></div>
      <div class="side-stat"><span>Work style</span><strong><?= e($job['work_mode']) ?></strong></div>
      <div class="side-stat"><span>Location</span><strong><?= e($job['location']) ?></strong></div>
      <div class="side-stat"><span>Posted</span><strong><?= e(date('d M Y', strtotime($job['created_at']))) ?></strong></div>
      <?php if ($user && $user['role'] === 'candidate' && $hasApplied): ?>
        <p class="callout" style="margin-top:21px">✓ You have already applied for this position.</p><a class="btn btn-light btn-block" href="my_applications.php">My applications</a>
      <?php elseif ($job['status'] !== 'open'): ?>
        <p class="callout" style="margin-top:21px">This listing is no longer accepting applications.</p>
      <?php elseif (!$user): ?>
        <p class="muted small" style="margin:20px 0 12px">Log in as a job seeker to apply.</p><a class="btn btn-primary btn-block" href="login.php">Log in to apply →</a>
      <?php elseif ($user['role'] === 'candidate'): ?>
        <form method="post" action="apply.php" style="margin-top:20px">
          <?= csrf_input() ?><input type="hidden" name="job_id" value="<?= (int)$job['id'] ?>">
          <label class="form-label" for="cover_letter">Short cover note (optional)</label><textarea class="form-control" id="cover_letter" name="cover_letter" rows="5" maxlength="2000" placeholder="Why would you be a good fit?"></textarea>
          <button class="btn btn-primary btn-block" style="margin-top:14px" type="submit">Submit application →</button>
        </form>
      <?php elseif ((int)$user['id'] === (int)$job['employer_id']): ?>
        <a class="btn btn-primary btn-block" style="margin-top:20px" href="applicants.php?job_id=<?= (int)$job['id'] ?>">View applicants →</a>
      <?php else: ?>
        <p class="callout" style="margin-top:20px">Employer accounts cannot apply for jobs.</p>
      <?php endif; ?>
    </aside>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

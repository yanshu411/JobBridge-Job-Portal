<?php
require __DIR__ . '/includes/bootstrap.php';
$page_title = 'Browse jobs';
$q = trim((string)($_GET['q'] ?? ''));
$location = trim((string)($_GET['location'] ?? ''));
$category = trim((string)($_GET['category'] ?? ''));
$type = trim((string)($_GET['type'] ?? ''));
if (strlen($q) > 120) $q = substr($q, 0, 120);
if (strlen($location) > 120) $location = substr($location, 0, 120);
$where = ["status = 'open'"];
$params = [];
if ($q !== '') {
    $where[] = '(title LIKE :qt OR company LIKE :qc OR description LIKE :qd)';
    $params[':qt'] = '%' . $q . '%';
    $params[':qc'] = '%' . $q . '%';
    $params[':qd'] = '%' . $q . '%';
}
if ($location !== '') {
    $where[] = '(location LIKE :location OR work_mode = :remote_mode)';
    $params[':location'] = '%' . $location . '%';
    $params[':remote_mode'] = strtolower($location) === 'remote' ? 'Remote' : '__no_match__';
}
if (in_array($category, job_categories(), true)) {
    $where[] = 'category = :category';
    $params[':category'] = $category;
}
if (in_array($type, job_types(), true)) {
    $where[] = 'job_type = :job_type';
    $params[':job_type'] = $type;
}
$sql = 'SELECT * FROM jobs WHERE ' . implode(' AND ', $where) . ' ORDER BY created_at DESC, id DESC LIMIT 100';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$jobs = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="page-heading"><div class="eyebrow">Find your match</div><h1>Explore opportunities</h1><p class="muted">Search roles from employers across different industries.</p></div>
  <form class="surface filter-bar" method="get" action="jobs.php">
    <input class="form-control" name="q" value="<?= e($q) ?>" placeholder="Job title or company" aria-label="Keyword">
    <input class="form-control" name="location" value="<?= e($location) ?>" placeholder="City or remote" aria-label="Location">
    <select class="form-control" name="category" aria-label="Category"><option value="">All categories</option><?php foreach (job_categories() as $cat): ?><option value="<?= e($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>><?= e($cat) ?></option><?php endforeach; ?></select>
    <select class="form-control" name="type" aria-label="Job type"><option value="">All job types</option><?php foreach (job_types() as $t): ?><option value="<?= e($t) ?>" <?= $type === $t ? 'selected' : '' ?>><?= e($t) ?></option><?php endforeach; ?></select>
    <button class="btn btn-primary" type="submit">Search</button>
  </form>
  <div class="section-caption"><h2><?= count($jobs) ?> <?= count($jobs) === 1 ? 'opportunity' : 'opportunities' ?> found</h2><span class="pagination-note">Showing up to 100 newest listings</span></div>
  <?php if ($jobs): ?><div class="job-grid"><?php foreach ($jobs as $job) require __DIR__ . '/includes/job_card.php'; ?></div>
  <?php else: ?><div class="surface empty"><div class="empty-icon">⌕</div><h3>No matching jobs found</h3><p>Try fewer filters or a different keyword to see more opportunities.</p><a class="btn btn-light" href="jobs.php">Clear filters</a></div><?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

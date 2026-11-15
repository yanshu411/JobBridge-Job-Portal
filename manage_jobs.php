<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_role('employer');
$page_title = 'Manage job listings';
$stmt = $pdo->prepare('SELECT j.*, (SELECT COUNT(*) FROM applications a WHERE a.job_id=j.id) AS application_count FROM jobs j WHERE j.employer_id=? ORDER BY j.created_at DESC,j.id DESC');
$stmt->execute([$user['id']]);
$jobs = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="page-heading page-tools"><div><div class="eyebrow">Employer workspace</div><h1>My job listings</h1><p class="muted">Manage openings and review your applicants.</p></div><a href="post_job.php" class="btn btn-primary">+ Post a job</a></div>
  <div class="surface padded">
    <?php if ($jobs): ?>
      <div class="table-wrap"><table class="data-table"><thead><tr><th>Job</th><th>Posted</th><th>Applications</th><th>Status</th><th>Actions</th></tr></thead><tbody>
      <?php foreach ($jobs as $job): ?><tr>
        <td><div class="table-title"><a href="job.php?id=<?= (int)$job['id'] ?>"><?= e($job['title']) ?></a></div><div class="table-muted"><?= e($job['company']) ?> · <?= e($job['location']) ?></div></td>
        <td><?= e(date('d M Y', strtotime($job['created_at']))) ?></td>
        <td><a class="link" href="applicants.php?job_id=<?= (int)$job['id'] ?>"><?= (int)$job['application_count'] ?> applicants</a></td>
        <td><span class="badge <?= $job['status'] === 'open' ? 'green' : 'gray' ?>"><?= e(ucfirst($job['status'])) ?></span></td>
        <td><div class="table-actions"><a class="btn btn-light btn-sm" href="edit_job.php?id=<?= (int)$job['id'] ?>">Edit</a><?php if ($job['status'] === 'open'): ?><form method="post" action="close_job.php" class="inline-form" data-confirm="Close this job listing? Existing applications will remain visible."><?= csrf_input() ?><input type="hidden" name="job_id" value="<?= (int)$job['id'] ?>"><button class="btn btn-danger btn-sm" type="submit">Close</button></form><?php endif; ?></div></td>
      </tr><?php endforeach; ?></tbody></table></div>
    <?php else: ?><div class="empty"><div class="empty-icon">▤</div><h3>No listings published</h3><p>Create your first opening and start receiving applications.</p><a href="post_job.php" class="btn btn-primary">+ Post your first job</a></div><?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

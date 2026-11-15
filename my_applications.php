<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_role('candidate');
$page_title = 'My applications';
$stmt = $pdo->prepare('SELECT a.id, a.status, a.created_at, j.id AS job_id, j.title, j.company, j.location, j.job_type FROM applications a JOIN jobs j ON j.id=a.job_id WHERE a.candidate_id=? ORDER BY a.created_at DESC, a.id DESC');
$stmt->execute([$user['id']]);
$applications = $stmt->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="page-heading"><div class="eyebrow">Application tracker</div><h1>My applications</h1><p class="muted">Keep track of the roles you've applied for.</p></div>
  <div class="surface padded">
    <?php if ($applications): ?>
    <div class="table-wrap"><table class="data-table"><thead><tr><th>Position</th><th>Location</th><th>Applied on</th><th>Status</th><th></th></tr></thead><tbody>
      <?php foreach ($applications as $item): ?><tr>
        <td><div class="table-title"><?= e($item['title']) ?></div><div class="table-muted"><?= e($item['company']) ?></div></td>
        <td><?= e($item['location']) ?><div class="table-muted"><?= e($item['job_type']) ?></div></td>
        <td><?= e(date('d M Y', strtotime($item['created_at']))) ?></td>
        <td><span class="badge <?= $item['status'] === 'shortlisted' ? 'green' : ($item['status'] === 'rejected' ? 'gray' : '') ?>"><?= e(ucfirst($item['status'])) ?></span></td>
        <td><a class="link" href="job.php?id=<?= (int)$item['job_id'] ?>">View job →</a></td>
      </tr><?php endforeach; ?></tbody></table></div>
    <?php else: ?><div class="empty"><div class="empty-icon">✉</div><h3>No applications yet</h3><p>Browse open positions and submit your first application.</p><a class="btn btn-primary" href="jobs.php">Find jobs →</a></div><?php endif; ?>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

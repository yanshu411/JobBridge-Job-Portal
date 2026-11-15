<?php
require __DIR__ . '/includes/bootstrap.php';
$user = require_role('employer');
$id = filter_input(INPUT_GET, 'job_id', FILTER_VALIDATE_INT);
if (!$id || $id < 1) { http_response_code(404); exit('Listing not found.'); }
$stmt = $pdo->prepare('SELECT * FROM jobs WHERE id=? AND employer_id=?');
$stmt->execute([$id, $user['id']]);
$job = $stmt->fetch();
if (!$job) { http_response_code(404); exit('Listing not found.'); }
$stmt = $pdo->prepare('SELECT a.*,u.full_name,u.email FROM applications a JOIN users u ON u.id=a.candidate_id WHERE a.job_id=? ORDER BY a.created_at DESC,a.id DESC');
$stmt->execute([$id]);
$applicants = $stmt->fetchAll();
$page_title = 'Applicants for ' . $job['title'];
require __DIR__ . '/includes/header.php';
?>
<div class="container">
  <div class="page-heading"><a class="link small" href="manage_jobs.php">← My listings</a><h1 style="margin-top:15px">Applications received</h1><p class="muted"><?= e($job['title']) ?> · <?= e($job['company']) ?> · <?= count($applicants) ?> applicant(s)</p></div>
  <?php if ($applicants): ?>
    <?php foreach ($applicants as $a): ?>
      <article class="surface padded" style="margin-bottom:16px">
        <div class="section-caption"><div><h2><?= e($a['full_name']) ?></h2><a class="link small break-text" href="mailto:<?= e($a['email']) ?>"><?= e($a['email']) ?></a></div><span class="badge <?= $a['status'] === 'shortlisted' ? 'green' : 'gray' ?>"><?= e(ucfirst($a['status'])) ?></span></div>
        <p class="small muted">Applied <?= e(date('d M Y, h:i A', strtotime($a['created_at']))) ?></p>
        <p class="detail-text" style="margin:15px 0"><?= e($a['cover_letter'] ?: 'No cover note provided.') ?></p>
        <form action="update_application.php" method="post" class="action-row">
          <?= csrf_input() ?><input type="hidden" name="application_id" value="<?= (int)$a['id'] ?>">
          <label class="sr-only" for="status-<?= (int)$a['id'] ?>">Application status</label>
          <select style="max-width:215px" id="status-<?= (int)$a['id'] ?>" class="form-control" name="status">
            <?php foreach (['submitted','reviewed','shortlisted','rejected'] as $status): ?><option value="<?= $status ?>" <?= $a['status'] === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option><?php endforeach; ?>
          </select>
          <button class="btn btn-light btn-sm" type="submit">Update status</button>
        </form>
      </article>
    <?php endforeach; ?>
  <?php else: ?><div class="surface empty"><div class="empty-icon">☷</div><h3>No applications yet</h3><p>Candidates who apply to this listing will appear here.</p></div><?php endif; ?>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>

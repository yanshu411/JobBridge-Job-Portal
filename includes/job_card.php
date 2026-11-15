<?php /** @var array $job */ ?>
<article class="surface job-card">
  <div class="card-top">
    <div class="company-icon" aria-hidden="true"><?= e(strtoupper(substr($job['company'], 0, 1))) ?></div>
    <span class="badge"><?= e($job['job_type']) ?></span>
  </div>
  <div>
    <h3><a href="job.php?id=<?= (int)$job['id'] ?>"><?= e($job['title']) ?></a></h3>
    <p class="company"><?= e($job['company']) ?></p>
  </div>
  <div class="tags">
    <span class="tag">⌖ <?= e($job['location']) ?></span>
    <span class="tag"><?= e($job['work_mode']) ?></span>
    <span class="tag"><?= e($job['category']) ?></span>
  </div>
  <div class="job-bottom">
    <div><div class="salary"><?= e(money_range($job['salary_min'], $job['salary_max'])) ?></div><span class="muted small"><?= e(age_label($job['created_at'])) ?></span></div>
    <a class="link small" href="job.php?id=<?= (int)$job['id'] ?>">View →</a>
  </div>
</article>

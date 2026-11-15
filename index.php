<?php
require __DIR__ . '/includes/bootstrap.php';
$page_title = 'Explore careers and opportunities';
$stats = [
    'jobs' => (int)$pdo->query("SELECT COUNT(*) FROM jobs WHERE status='open'")->fetchColumn(),
    'companies' => (int)$pdo->query("SELECT COUNT(DISTINCT company) FROM jobs WHERE status='open'")->fetchColumn(),
];
$featured = $pdo->query("SELECT * FROM jobs WHERE status='open' ORDER BY created_at DESC, id DESC LIMIT 6")->fetchAll();
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="container hero-inner">
    <div>
      <div class="eyebrow">Your future starts here</div>
      <h1>Find the right job.<br><em>Build your future.</em></h1>
      <p>Discover internships, entry-level roles and exciting career opportunities, all in one place.</p>
      <form action="jobs.php" method="get" class="search-bar">
        <label class="sr-only" for="hero-q">Search jobs</label><input id="hero-q" class="search-input" name="q" placeholder="Job title, keyword or company">
        <span class="search-divider"></span>
        <label class="sr-only" for="hero-location">Location</label><input id="hero-location" class="search-input" name="location" placeholder="City or remote">
        <button class="btn" type="submit">Search jobs →</button>
      </form>
    </div>
    <div class="hero-art" aria-hidden="true">
      <div class="hero-stat"><strong><?= number_format($stats['jobs']) ?>+</strong><span>Open opportunities</span></div>
      <div class="hero-stat"><strong><?= number_format($stats['companies']) ?>+</strong><span>Hiring companies</span></div>
      <div class="hero-stat"><strong>One place</strong><span>To discover your next step</span></div>
    </div>
  </div>
</section>
<section class="section">
  <div class="container">
    <div class="section-head"><div><div class="eyebrow">Discover opportunities</div><h2>Latest job openings</h2><p>Fresh roles added by employers on JobBridge.</p></div><a class="btn btn-light" href="jobs.php">View all jobs →</a></div>
    <?php if ($featured): ?>
      <div class="job-grid"><?php foreach ($featured as $job) require __DIR__ . '/includes/job_card.php'; ?></div>
    <?php else: ?>
      <div class="surface empty"><div class="empty-icon">▤</div><h3>No jobs posted yet</h3><p>Employers can register and publish their first listing. New openings will appear here.</p><a class="btn btn-primary" href="register.php">Get started</a></div>
    <?php endif; ?>
  </div>
</section>
<section class="section-tight"><div class="container">
  <div class="section-head"><div><div class="eyebrow">Made for everyone</div><h2>Everything you need to get started</h2></div></div>
  <div class="features">
    <div class="surface feature"><div class="feature-icon">⌕</div><h3>Smart job search</h3><p>Filter vacancies by keyword, city, category and employment type.</p></div>
    <div class="surface feature"><div class="feature-icon">✓</div><h3>Simple applications</h3><p>Apply in minutes and follow your application status from your dashboard.</p></div>
    <div class="surface feature"><div class="feature-icon">▦</div><h3>Employer workspace</h3><p>Publish openings, manage listings and review candidate applications.</p></div>
  </div>
</div></section>
<section class="section-tight"><div class="container"><div class="cta"><div><h2>Hiring for your team?</h2><p>Create a free employer account and reach motivated candidates.</p></div><a class="btn btn-light" href="register.php">Post your first job →</a></div></div></section>
<?php require __DIR__ . '/includes/footer.php'; ?>

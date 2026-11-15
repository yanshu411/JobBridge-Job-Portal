<?php
/** @var string $page_title */
$navUser = current_user();
$page_title = $page_title ?? 'Find your next opportunity';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="theme-color" content="#11223c">
  <meta name="description" content="JobBridge helps candidates find work and employers manage job listings.">
  <title><?= e($page_title) ?> · JobBridge</title>
  <link rel="stylesheet" href="assets/style.css">
  <script defer src="assets/app.js"></script>
</head>
<body>
<header class="site-header">
  <div class="container header-inner">
    <a class="brand" href="index.php" aria-label="JobBridge home"><span class="brand-icon">J</span><span>Job<span class="brand-accent">Bridge</span></span></a>
    <button class="menu-toggle" type="button" aria-controls="site-nav" aria-expanded="false">☰ <span class="sr-only">Toggle menu</span></button>
    <nav id="site-nav" class="site-nav" aria-label="Main navigation">
      <a href="jobs.php">Explore jobs</a>
      <?php if ($navUser): ?>
        <a href="dashboard.php">Dashboard</a>
        <?php if ($navUser['role'] === 'employer'): ?>
          <a href="manage_jobs.php">My listings</a>
          <a class="btn btn-primary btn-sm" href="post_job.php">+ Post a job</a>
        <?php else: ?>
          <a href="my_applications.php">My applications</a>
        <?php endif; ?>
        <span class="nav-user">Hi, <?= e(explode(' ', trim($navUser['full_name']))[0]) ?></span>
        <form method="post" action="logout.php" class="nav-logout"><?= csrf_input() ?><button class="nav-text-button" type="submit">Log out</button></form>
      <?php else: ?>
        <a href="login.php">Log in</a>
        <a class="btn btn-primary btn-sm" href="register.php">Get started</a>
      <?php endif; ?>
    </nav>
  </div>
</header>
<?php foreach (flashes() as $message): ?>
  <div class="container flash-wrap"><div class="flash flash-<?= e($message['type']) ?>" role="status"><?= e($message['message']) ?></div></div>
<?php endforeach; ?>
<main>

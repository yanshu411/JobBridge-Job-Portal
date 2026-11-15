<?php
require __DIR__ . '/includes/bootstrap.php';
if (current_user()) redirect('dashboard.php');
$page_title = 'Log in';
$error = '';
$email = trim((string)($_POST['email'] ?? ''));
if (is_post()) {
    verify_csrf();
    $password = (string)($_POST['password'] ?? '');
    $stmt = $pdo->prepare('SELECT id, password_hash FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int)$user['id'];
        flash('success', 'You are now logged in.');
        redirect('dashboard.php');
    }
    $error = 'Incorrect email or password.';
}
require __DIR__ . '/includes/header.php';
?>
<div class="container"><div class="auth-container"><div class="surface auth-box">
  <div class="eyebrow">Welcome back</div><h1>Log in to JobBridge</h1><p class="muted">Access your dashboard and opportunities.</p>
  <?php if ($error): ?><div class="error-list"><?= e($error) ?></div><?php endif; ?>
  <form method="post" action="login.php"><?= csrf_input() ?>
    <div class="form-group"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" name="email" type="email" maxlength="190" autocomplete="email" value="<?= e($email) ?>" required></div>
    <div class="form-group"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" autocomplete="current-password" required></div>
    <button class="btn btn-primary btn-block" type="submit">Log in →</button>
  </form>
  <p class="auth-switch">Don't have an account? <a class="link" href="register.php">Sign up</a></p>
</div></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

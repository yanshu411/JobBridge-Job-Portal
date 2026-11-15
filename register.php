<?php
require __DIR__ . '/includes/bootstrap.php';
if (current_user()) redirect('dashboard.php');
$page_title = 'Create an account';
$errors = [];
$name = trim((string)($_POST['full_name'] ?? ''));
$email = strtolower(trim((string)($_POST['email'] ?? '')));
$role = (string)($_POST['role'] ?? 'candidate');
if (is_post()) {
    verify_csrf();
    $password = (string)($_POST['password'] ?? '');
    $confirmation = (string)($_POST['confirm_password'] ?? '');
    if (char_length($name) < 2 || char_length($name) > 100) $errors[] = 'Enter your name (2–100 characters).';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190) $errors[] = 'Enter a valid email address.';
    if (!in_array($role, ['candidate', 'employer'], true)) $errors[] = 'Select a valid account type.';
    if (strlen($password) < 8 || strlen($password) > 72) $errors[] = 'Password must contain 8–72 characters.';
    if ($password !== $confirmation) $errors[] = 'Passwords do not match.';
    if (!$errors) {
        $exists = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $exists->execute([$email]);
        if ($exists->fetch()) $errors[] = 'That email address is already registered.';
    }
    if (!$errors) {
        try {
            $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password_hash, role) VALUES (?, ?, ?, ?)');
            $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $role]);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            flash('success', 'Your account is ready. Welcome to JobBridge!');
            redirect('dashboard.php');
        } catch (PDOException $e) {
            $errors[] = 'Could not create your account. Try a different email.';
        }
    }
}
require __DIR__ . '/includes/header.php';
?>
<div class="container"><div class="auth-container"><div class="surface auth-box">
  <div class="eyebrow">Join JobBridge</div><h1>Create your account</h1><p class="muted">One account. More opportunities.</p>
  <?php if ($errors): ?><div class="error-list"><ul><?php foreach ($errors as $error): ?><li><?= e($error) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
  <form method="post" action="register.php">
    <?= csrf_input() ?>
    <div class="form-group"><label class="form-label" for="full_name">Full name</label><input class="form-control" id="full_name" name="full_name" maxlength="100" autocomplete="name" value="<?= e($name) ?>" required></div>
    <div class="form-group"><label class="form-label" for="email">Email address</label><input class="form-control" id="email" name="email" type="email" maxlength="190" autocomplete="email" value="<?= e($email) ?>" required></div>
    <div class="form-group"><span class="form-label">I want to...</span><div class="role-picker"><label class="role-choice"><input type="radio" name="role" value="candidate" <?= $role === 'candidate' ? 'checked' : '' ?>> Find a job</label><label class="role-choice"><input type="radio" name="role" value="employer" <?= $role === 'employer' ? 'checked' : '' ?>> Hire talent</label></div></div>
    <div class="form-grid"><div class="form-group"><label class="form-label" for="password">Password</label><input class="form-control" id="password" name="password" type="password" minlength="8" maxlength="72" autocomplete="new-password" required></div><div class="form-group"><label class="form-label" for="confirm_password">Confirm password</label><input class="form-control" id="confirm_password" name="confirm_password" type="password" minlength="8" maxlength="72" autocomplete="new-password" required></div></div>
    <p class="password-hint">Use at least 8 characters. This is an educational app; do not reuse an important password.</p>
    <button class="btn btn-primary btn-block" type="submit">Create account →</button>
  </form>
  <p class="auth-switch">Already a member? <a class="link" href="login.php">Log in</a></p>
</div></div></div>
<?php require __DIR__ . '/includes/footer.php'; ?>

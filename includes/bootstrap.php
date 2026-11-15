<?php
declare(strict_types=1);

$config = require dirname(__DIR__) . '/config.php';

$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || ((int)($_SERVER['SERVER_PORT'] ?? 0) === 443);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('jobbridge_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

try {
    $dsn = sprintf(
        'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
        $config['db_host'], $config['db_port'], $config['db_name']
    );
    $pdo = new PDO($dsn, $config['db_user'], $config['db_pass'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    http_response_code(503);
    exit('Database connection failed. Start MySQL, import database.sql, and check config.php.');
}

function e($value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
function url(string $path): string {
    return e($path);
}
function redirect(string $path): void {
    header('Location: ' . $path, true, 303);
    exit;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function csrf_input(): string {
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}
function verify_csrf(): void {
    if (!hash_equals(csrf_token(), (string)($_POST['csrf_token'] ?? ''))) {
        http_response_code(403);
        exit('Invalid security token. Refresh the page and try again.');
    }
}
function flash(string $type, string $message): void {
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}
function flashes(): array {
    $messages = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $messages;
}
function current_user(): ?array {
    global $pdo;
    if (empty($_SESSION['user_id'])) return null;
    $stmt = $pdo->prepare('SELECT id, full_name, email, role FROM users WHERE id = ?');
    $stmt->execute([(int)$_SESSION['user_id']]);
    $user = $stmt->fetch();
    return $user ?: null;
}
function require_login(): array {
    $user = current_user();
    if (!$user) {
        flash('info', 'Please log in to continue.');
        redirect('login.php');
    }
    return $user;
}
function require_role(string $role): array {
    $user = require_login();
    if ($user['role'] !== $role) {
        http_response_code(403);
        exit('Access denied: this page is for ' . e($role) . ' accounts.');
    }
    return $user;
}
function is_post(): bool {
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}
function money_range($min, $max): string {
    if ($min === null && $max === null) return 'Salary not disclosed';
    if ($min !== null && $max !== null) return '₹' . number_format((int)$min) . ' – ₹' . number_format((int)$max) . ' / month';
    if ($min !== null) return 'From ₹' . number_format((int)$min) . ' / month';
    return 'Up to ₹' . number_format((int)$max) . ' / month';
}
function age_label(string $timestamp): string {
    $days = max(0, (int)floor((time() - strtotime($timestamp)) / 86400));
    if ($days === 0) return 'Today';
    if ($days === 1) return 'Yesterday';
    return $days . ' days ago';
}
function char_length(string $value): int {
    if (function_exists('mb_strlen')) return mb_strlen($value, 'UTF-8');
    $count = preg_match_all('/./us', $value);
    return $count === false ? strlen($value) : $count;
}
function job_categories(): array {
    return ['Technology','Design','Marketing','Finance','Business','Operations','Other'];
}
function job_types(): array {
    return ['Full-time','Part-time','Internship','Contract'];
}
function work_modes(): array {
    return ['On-site','Hybrid','Remote'];
}
function validate_job(array $input): array {
    $data = [];
    foreach (['title','company','location','category','job_type','work_mode','description','requirements'] as $key) {
        $data[$key] = trim((string)($input[$key] ?? ''));
    }
    $errors = [];
    foreach (['title' => 120, 'company' => 120, 'location' => 120] as $key => $length) {
        if ($data[$key] === '' || char_length($data[$key]) > $length) {
            $errors[] = ucfirst($key) . ' is required (up to ' . $length . ' characters).';
        }
    }
    if (!in_array($data['category'], job_categories(), true)) $errors[] = 'Choose a valid category.';
    if (!in_array($data['job_type'], job_types(), true)) $errors[] = 'Choose a valid employment type.';
    if (!in_array($data['work_mode'], work_modes(), true)) $errors[] = 'Choose a valid work arrangement.';
    if (char_length($data['description']) < 30 || char_length($data['description']) > 10000) {
        $errors[] = 'Description must be 30 to 10,000 characters.';
    }
    if (char_length($data['requirements']) > 5000) $errors[] = 'Requirements must be under 5,000 characters.';

    foreach (['salary_min', 'salary_max'] as $key) {
        $raw = trim((string)($input[$key] ?? ''));
        if ($raw === '') {
            $data[$key] = null;
        } elseif (!ctype_digit($raw) || strlen($raw) > 9 || (int)$raw > 100000000) {
            $errors[] = 'Salary must be a whole rupee amount up to 100,000,000.';
            $data[$key] = null;
        } else {
            $data[$key] = (int)$raw;
        }
    }
    if ($data['salary_min'] !== null && $data['salary_max'] !== null && $data['salary_min'] > $data['salary_max']) {
        $errors[] = 'Minimum salary cannot exceed maximum salary.';
    }
    return [$data, $errors];
}

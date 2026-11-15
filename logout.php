<?php
require __DIR__ . '/includes/bootstrap.php';
if (!is_post()) {
    http_response_code(405);
    exit('Method not allowed.');
}
verify_csrf();
$_SESSION = [];
session_regenerate_id(true);
flash('info', 'You have logged out.');
redirect('index.php');

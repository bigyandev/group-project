<?php
// ============================================================
//  includes/db.php
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'root');
define('DB_NAME', 'bookloop_db');

define('SITE_NAME', 'BookLoop');
define('BASE_PATH', '/bookloop');   // change to match your htdocs folder name

define('UPLOAD_DIR',  __DIR__ . '/../uploads/');
define('COVERS_DIR',  UPLOAD_DIR . 'covers/');
define('AVATARS_DIR', UPLOAD_DIR . 'avatars/');
define('COVERS_URL',  BASE_PATH . '/uploads/covers/');
define('AVATARS_URL', BASE_PATH . '/uploads/avatars/');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('Database connection failed: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');

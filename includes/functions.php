<?php
function startSession() {
    if (session_status() === PHP_SESSION_NONE) session_start();
}
function isLoggedIn() { return isset($_SESSION['user_id']); }
function isAdmin()    { return isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1; }

function requireLogin() {
    if (!isLoggedIn()) { header('Location: ' . BASE_PATH . '/login.php'); exit; }
}
function requireAdmin() {
    requireLogin();
    if (!isAdmin()) { header('Location: ' . BASE_PATH . '/index.php'); exit; }
}

function csrfToken() {
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}
function csrfField() { return '<input type="hidden" name="csrf_token" value="' . csrfToken() . '">'; }
function csrfCheck() { return hash_equals($_SESSION['csrf_token'] ?? '', $_POST['csrf_token'] ?? ''); }

function setFlash($type, $message) { $_SESSION['flash'] = ['type' => $type, 'message' => $message]; }
function getFlash() {
    if (!empty($_SESSION['flash'])) { $f = $_SESSION['flash']; unset($_SESSION['flash']); return $f; }
    return null;
}

function e($str) { return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8'); }

function redirect($path) { header('Location: ' . BASE_PATH . $path); exit; }

function createNotification($conn, $userId, $type, $title, $message, $link = null) {
    $stmt = $conn->prepare('INSERT INTO notifications (user_id, type, title, message, link) VALUES (?,?,?,?,?)');
    $stmt->bind_param('issss', $userId, $type, $title, $message, $link);
    $stmt->execute();
    $stmt->close();
}

function unreadCount($conn) {
    if (!isLoggedIn()) return 0;
    $uid  = $_SESSION['user_id'];
    $stmt = $conn->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND is_read = 0');
    $stmt->bind_param('i', $uid);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();
    return (int)$count;
}

function renderStars($rating) {
    $rating = round((float)$rating); $out = '';
    for ($i = 1; $i <= 5; $i++) $out .= $i <= $rating ? '★' : '☆';
    return $out;
}

function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60)     return 'just now';
    if ($diff < 3600)   return floor($diff / 60) . ' min ago';
    if ($diff < 86400)  return floor($diff / 3600) . ' hr ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('d M Y', strtotime($datetime));
}

function conditionClass($c) {
    return match($c) {
        'New' => 'badge-green', 'Like New' => 'badge-blue', 'Good' => 'badge-yellow',
        'Fair' => 'badge-orange', 'Poor' => 'badge-red', default => 'badge-gray',
    };
}

function statusClass($s) {
    return match($s) {
        'pending' => 'badge-yellow', 'accepted' => 'badge-blue', 'completed' => 'badge-green',
        'declined' => 'badge-red', 'cancelled' => 'badge-gray', default => 'badge-gray',
    };
}

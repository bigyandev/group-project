<?php
// ============================================================
//  notifications.php  –  View & mark notifications
// ============================================================
$pageTitle = 'Notifications';
$pageCss   = 'notifications';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
startSession();
requireLogin();

$userId = $_SESSION['user_id'];

// Mark all as read
if (isset($_GET['mark_read']) && $_GET['mark_read'] === '1') {
    $conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $userId");
    header('Location: ' . BASE_PATH . '/notifications.php'); exit;;
}

// Fetch all notifications for this user
$stmt = $conn->prepare(
    "SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 60"
);
$stmt->bind_param('i', $userId);
$stmt->execute();
$notifs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Mark all as read on page load
$conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = $userId AND is_read = 0");

$typeIcons = [
    'request'  => '📬',
    'accepted' => '✅',
    'declined' => '❌',
    'completed'=> '🎉',
    'system'   => '🔔',
];

require_once __DIR__ . '/includes/header.php';
?>

<div class="notif-page">
<div class="container">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <h1 style="font-size:1.6rem;font-weight:800;">Notifications</h1>
        <?php if ($notifs): ?>
        <a href="?mark_read=1" class="btn btn-outline btn-sm">Mark all read</a>
        <?php endif; ?>
    </div>

    <div class="notif-list">
    <?php if ($notifs): ?>
        <?php foreach ($notifs as $n): ?>
        <div class="notif-item <?php echo !$n['is_read'] ? 'unread' : ''; ?>">
            <div class="notif-item-icon">
                <?php echo $typeIcons[$n['type']] ?? '🔔'; ?>
            </div>
            <div style="flex:1;">
                <div class="notif-title"><?php echo e($n['title']); ?></div>
                <?php if ($n['message']): ?>
                <div class="notif-message"><?php echo e($n['message']); ?></div>
                <?php endif; ?>
                <div class="notif-time"><?php echo timeAgo($n['created_at']); ?></div>
            </div>
            <?php if ($n['link']): ?>
            <a href="<?php echo e($n['link']); ?>" class="btn btn-outline btn-sm">View</a>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">🔔</div>
            <h3>All caught up!</h3>
            <p>No notifications yet. They'll appear here when someone requests your books.</p>
        </div>
    <?php endif; ?>
    </div>

</div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

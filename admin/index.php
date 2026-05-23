<?php
$pageTitle = 'Admin Dashboard';
$pageCss   = 'admin';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';
startSession();
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfCheck()) {
    $action   = $_POST['action']    ?? '';
    $targetId = (int)($_POST['target_id'] ?? 0);

    switch ($action) {
        case 'suspend_user':
            $conn->query("UPDATE users SET is_active = 0 WHERE user_id = $targetId AND is_admin = 0");
            createNotification($conn, $targetId, 'system', 'Account Suspended',
                'Your account has been suspended by an administrator.');
            setFlash('success', 'User suspended.');
            break;
        case 'restore_user':
            $conn->query("UPDATE users SET is_active = 1 WHERE user_id = $targetId AND is_admin = 0");
            createNotification($conn, $targetId, 'system', 'Account Restored',
                'Your account has been restored. Welcome back!');
            setFlash('success', 'User restored.');
            break;
        case 'delete_user':
            $del = $conn->prepare("DELETE FROM users WHERE user_id = ? AND is_admin = 0");
            $del->bind_param('i', $targetId); $del->execute(); $del->close();
            setFlash('success', 'User deleted.');
            break;
        case 'delete_book':
            $del = $conn->prepare("DELETE FROM books WHERE book_id = ?");
            $del->bind_param('i', $targetId); $del->execute(); $del->close();
            setFlash('success', 'Book deleted.');
            break;
        case 'cancel_request':
            $req = $conn->query(
                "SELECT er.requester_id, er.owner_id, er.book_id, b.title
                 FROM exchange_requests er JOIN books b ON b.book_id = er.book_id
                 WHERE er.request_id = $targetId"
            )->fetch_assoc();
            if ($req) {
                $conn->query("UPDATE exchange_requests SET status='cancelled' WHERE request_id=$targetId");
                $conn->query("UPDATE books SET is_available=1 WHERE book_id={$req['book_id']}");
                $notice = 'Your exchange for "' . $req['title'] . '" was cancelled by an administrator.';
                createNotification($conn, $req['requester_id'], 'system', 'Request Cancelled', $notice);
                createNotification($conn, $req['owner_id'],     'system', 'Request Cancelled', $notice);
            }
            setFlash('success', 'Request cancelled.');
            break;
        case 'broadcast':
            $title   = trim($_POST['notif_title']   ?? '');
            $message = trim($_POST['notif_message'] ?? '');
            if ($title !== '') {
                $all = $conn->query("SELECT user_id FROM users WHERE is_active=1 AND is_admin=0");
                while ($u = $all->fetch_row()) {
                    createNotification($conn, $u[0], 'system', $title, $message);
                }
                setFlash('success', 'Notification broadcast sent.');
            }
            break;
    }
    header('Location: ' . BASE_PATH . '/admin/index.php');
    exit;
}

$stats = [
    'users'     => $conn->query("SELECT COUNT(*) FROM users WHERE is_admin=0")->fetch_row()[0],
    'books'     => $conn->query("SELECT COUNT(*) FROM books")->fetch_row()[0],
    'available' => $conn->query("SELECT COUNT(*) FROM books WHERE is_available=1")->fetch_row()[0],
    'swaps'     => $conn->query("SELECT COUNT(*) FROM exchange_requests WHERE status='completed'")->fetch_row()[0],
    'pending'   => $conn->query("SELECT COUNT(*) FROM exchange_requests WHERE status='pending'")->fetch_row()[0],
    'suspended' => $conn->query("SELECT COUNT(*) FROM users WHERE is_active=0 AND is_admin=0")->fetch_row()[0],
];

$users = $conn->query(
    "SELECT u.user_id, u.username, u.email, u.first_name, u.last_name,
            u.location, u.is_active, u.created_at, u.last_login,
            COUNT(DISTINCT b.book_id) AS book_count
     FROM users u LEFT JOIN books b ON b.user_id = u.user_id
     WHERE u.is_admin = 0
     GROUP BY u.user_id ORDER BY u.created_at DESC"
)->fetch_all(MYSQLI_ASSOC);

$books = $conn->query(
    "SELECT b.book_id, b.title, b.author, b.genre, b.condition_status,
            b.is_available, b.views, b.listed_at, u.username
     FROM books b JOIN users u ON u.user_id = b.user_id
     ORDER BY b.listed_at DESC"
)->fetch_all(MYSQLI_ASSOC);

$requests = $conn->query(
    "SELECT er.request_id, er.status, er.created_at,
            b.title AS book_title, b.book_id,
            r.username AS requester, o.username AS owner
     FROM exchange_requests er
     JOIN books b ON b.book_id = er.book_id
     JOIN users r ON r.user_id = er.requester_id
     JOIN users o ON o.user_id = er.owner_id
     ORDER BY er.created_at DESC LIMIT 50"
)->fetch_all(MYSQLI_ASSOC);

$activeTab = $_GET['tab'] ?? 'users';
$csrf      = csrfToken();
require_once __DIR__ . '/../includes/header.php';
?>

<div class="admin-page">
<div class="container">

    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px;">
        <h1>⚙ Admin Dashboard</h1>
        <span style="font-size:.85rem;color:#78716C;">Logged in as <strong><?php echo e($_SESSION['username']); ?></strong></span>
    </div>

    <div class="admin-stats">
        <div class="admin-stat-card"><div class="admin-stat-num"><?php echo $stats['users']; ?></div><div class="admin-stat-lbl">Total Users</div></div>
        <div class="admin-stat-card" style="border-top-color:#EF4444"><div class="admin-stat-num"><?php echo $stats['suspended']; ?></div><div class="admin-stat-lbl">Suspended</div></div>
        <div class="admin-stat-card" style="border-top-color:#3B82F6"><div class="admin-stat-num"><?php echo $stats['books']; ?></div><div class="admin-stat-lbl">Total Books</div></div>
        <div class="admin-stat-card" style="border-top-color:#22C55E"><div class="admin-stat-num"><?php echo $stats['available']; ?></div><div class="admin-stat-lbl">Available Now</div></div>
        <div class="admin-stat-card" style="border-top-color:#8B5CF6"><div class="admin-stat-num"><?php echo $stats['swaps']; ?></div><div class="admin-stat-lbl">Swaps Done</div></div>
        <div class="admin-stat-card" style="border-top-color:#F59E0B"><div class="admin-stat-num"><?php echo $stats['pending']; ?></div><div class="admin-stat-lbl">Pending</div></div>
    </div>

    

    <!-- Users -->
    <div class="tab-panel <?php echo $activeTab==='users'?'active':''; ?>" id="tab-users">
        <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>#</th><th>Username</th><th>Name</th><th>Email</th><th>Location</th><th>Books</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): ?>
            <tr>
                <td><?php echo $u['user_id']; ?></td>
                <td><strong><?php echo e($u['username']); ?></strong></td>
                <td><?php echo e($u['first_name'].' '.$u['last_name']); ?></td>
                <td style="font-size:.82rem;"><?php echo e($u['email']); ?></td>
                <td><?php echo e($u['location'] ?? '—'); ?></td>
                <td><?php echo $u['book_count']; ?></td>
                <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                <td><span class="badge <?php echo $u['is_active']?'badge-green':'badge-red'; ?>"><?php echo $u['is_active']?'Active':'Suspended'; ?></span></td>
                <td>
                    <div style="display:flex;gap:6px;flex-wrap:wrap;">
                    <?php if ($u['is_active']): ?>
                    <form method="POST"><?php echo csrfField(); ?><input type="hidden" name="target_id" value="<?php echo $u['user_id']; ?>"><input type="hidden" name="action" value="suspend_user"><button class="btn btn-warning btn-sm">Suspend</button></form>
                    <?php else: ?>
                    <form method="POST"><?php echo csrfField(); ?><input type="hidden" name="target_id" value="<?php echo $u['user_id']; ?>"><input type="hidden" name="action" value="restore_user"><button class="btn btn-success btn-sm">Restore</button></form>
                    <?php endif; ?>
                    <form method="POST" onsubmit="return confirm('Delete this user permanently?')"><?php echo csrfField(); ?><input type="hidden" name="target_id" value="<?php echo $u['user_id']; ?>"><input type="hidden" name="action" value="delete_user"><button class="btn btn-danger btn-sm">Delete</button></form>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>

    <!-- Books -->
    <div class="tab-panel <?php echo $activeTab==='books'?'active':''; ?>" id="tab-books">
        <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>#</th><th>Title</th><th>Author</th><th>Genre</th><th>Owner</th><th>Views</th><th>Listed</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($books as $b): ?>
            <tr>
                <td><?php echo $b['book_id']; ?></td>
                <td><a href="<?php echo BASE_PATH; ?>/book.php?id=<?php echo $b['book_id']; ?>" style="color:#D97706;font-weight:600;" target="_blank"><?php echo e($b['title']); ?> ↗</a></td>
                <td><?php echo e($b['author']); ?></td>
                <td style="font-size:.82rem;"><?php echo e($b['genre']); ?></td>
                <td><?php echo e($b['username']); ?></td>
                <td><?php echo $b['views']; ?></td>
                <td><?php echo date('d M Y', strtotime($b['listed_at'])); ?></td>
                <td><span class="badge <?php echo $b['is_available']?'badge-green':'badge-gray'; ?>"><?php echo $b['is_available']?'Available':'Unavailable'; ?></span></td>
                <td>
                    <form method="POST" onsubmit="return confirm('Delete this book?')"><?php echo csrfField(); ?><input type="hidden" name="target_id" value="<?php echo $b['book_id']; ?>"><input type="hidden" name="action" value="delete_book"><button class="btn btn-danger btn-sm">Delete</button></form>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>

    <!-- Requests -->
    <div class="tab-panel <?php echo $activeTab==='requests'?'active':''; ?>" id="tab-requests">
        <div style="overflow-x:auto;">
        <table class="data-table">
            <thead><tr><th>#</th><th>Book</th><th>Requester</th><th>Owner</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($requests as $r): ?>
            <tr>
                <td><?php echo $r['request_id']; ?></td>
                <td><a href="<?php echo BASE_PATH; ?>/book.php?id=<?php echo $r['book_id']; ?>" style="color:#D97706;" target="_blank"><?php echo e($r['book_title']); ?> ↗</a></td>
                <td><?php echo e($r['requester']); ?></td>
                <td><?php echo e($r['owner']); ?></td>
                <td><span class="badge <?php echo statusClass($r['status']); ?>"><?php echo ucfirst($r['status']); ?></span></td>
                <td><?php echo date('d M Y', strtotime($r['created_at'])); ?></td>
                <td>
                    <?php if (in_array($r['status'], ['pending','accepted'])): ?>
                    <form method="POST" onsubmit="return confirm('Force-cancel this request?')"><?php echo csrfField(); ?><input type="hidden" name="target_id" value="<?php echo $r['request_id']; ?>"><input type="hidden" name="action" value="cancel_request"><button class="btn btn-warning btn-sm">Cancel</button></form>
                    <?php else: ?><span style="color:#A8A29E;font-size:.82rem;">—</span><?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>

    <!-- Broadcast -->
    <div class="tab-panel <?php echo $activeTab==='broadcast'?'active':''; ?>" id="tab-broadcast">
        <p style="font-size:.88rem;color:#78716C;background:#FFFBEB;border-left:4px solid #F59E0B;padding:12px 16px;border-radius:0 8px 8px 0;margin-bottom:20px;">
            Send a notification to every active user's notification bell.
        </p>
        <div style="max-width:560px;">
            <form method="POST">
                <?php echo csrfField(); ?>
                <input type="hidden" name="action" value="broadcast">
                <div class="form-group">
                    <label class="form-label" for="notif_title">Title <span class="req">*</span></label>
                    <input type="text" id="notif_title" name="notif_title" class="form-control"
                           placeholder="e.g. Scheduled Maintenance on Saturday" required maxlength="200">
                </div>
                <div class="form-group">
                    <label class="form-label" for="notif_message">Message</label>
                    <textarea id="notif_message" name="notif_message" class="form-control"
                              placeholder="Optional details…" maxlength="500"></textarea>
                </div>
                <button type="submit" class="btn btn-accent btn-lg"
                        onclick="return confirm('Send to all <?php echo $stats['users']; ?> users?')">
                    📢 Send to All Users
                </button>
            </form>
        </div>
    </div>

</div>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-panel').forEach(function(p){ p.classList.remove('active'); });
    document.querySelectorAll('.tab-btn').forEach(function(b){   b.classList.remove('active'); });
    document.getElementById('tab-' + tab).classList.add('active');
    event.currentTarget.classList.add('active');
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>

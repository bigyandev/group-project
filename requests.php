<?php
$pageTitle = 'Requests';
$pageCss   = 'dashboard';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
startSession();
requireLogin();

$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfCheck()) {
    $requestId = (int)($_POST['request_id'] ?? 0);
    $action    = $_POST['action'] ?? '';

    $allowed = ['accept','decline','complete','cancel'];
    if ($requestId > 0 && in_array($action, $allowed)) {

        $newStatus = match($action) {
            'accept'   => 'accepted',
            'decline'  => 'declined',
            'complete' => 'completed',
            'cancel'   => 'cancelled',
        };

        if (in_array($action, ['accept','decline'])) {
            $upd = $conn->prepare(
                "UPDATE exchange_requests SET status = ?
                 WHERE request_id = ? AND owner_id = ? AND status = 'pending'"
            );
        } elseif ($action === 'complete') {
            $upd = $conn->prepare(
                "UPDATE exchange_requests SET status = ?
                 WHERE request_id = ? AND owner_id = ? AND status = 'accepted'"
            );
        } else {
            $upd = $conn->prepare(
                "UPDATE exchange_requests SET status = ?
                 WHERE request_id = ? AND requester_id = ? AND status IN ('pending','accepted')"
            );
        }

        $upd->bind_param('sii', $newStatus, $requestId, $userId);
        $upd->execute();
        $upd->close();

        $req = $conn->query(
            "SELECT er.*, b.title, b.book_id
             FROM exchange_requests er
             JOIN books b ON b.book_id = er.book_id
             WHERE er.request_id = $requestId"
        )->fetch_assoc();

        if ($req) {
            $bookId = (int)$req['book_id'];

            if ($action === 'accept') {
                $conn->query("UPDATE books SET is_available = 0 WHERE book_id = $bookId");
                $others = $conn->query(
                    "SELECT request_id, requester_id FROM exchange_requests
                     WHERE book_id = $bookId AND request_id != $requestId AND status = 'pending'"
                );
                while ($other = $others->fetch_assoc()) {
                    $conn->query("UPDATE exchange_requests SET status = 'declined'
                                  WHERE request_id = {$other['request_id']}");
                    createNotification($conn, $other['requester_id'], 'request',
                        'Request Declined',
                        'Your request for "' . $req['title'] . '" was declined because the owner accepted another request.',
                        BASE_PATH . '/requests.php'
                    );
                }
            }

            if ($action === 'decline' || $action === 'cancel') {
                $activeCount = $conn->query(
                    "SELECT COUNT(*) FROM exchange_requests
                     WHERE book_id = $bookId AND status = 'accepted'"
                )->fetch_row()[0];
                if ($activeCount === 0) {
                    $conn->query("UPDATE books SET is_available = 1 WHERE book_id = $bookId");
                }
            }

            $notifyUserId = ($action === 'cancel') ? $req['owner_id'] : $req['requester_id'];
            createNotification($conn, $notifyUserId, 'request',
                'Request ' . ucfirst($newStatus),
                'Your request for "' . $req['title'] . '" has been ' . $newStatus . '.',
                BASE_PATH . '/requests.php'
            );
        }

        setFlash('success', 'Request ' . $newStatus . '.');
    }

    header('Location: ' . BASE_PATH . '/requests.php');
    exit;
}

$incoming = $conn->prepare(
    "SELECT er.request_id, er.status, er.message, er.created_at,
            b.book_id, b.title AS book_title,
            u.username AS requester_name
     FROM exchange_requests er
     JOIN books b ON b.book_id = er.book_id
     JOIN users u ON u.user_id = er.requester_id
     WHERE er.owner_id = ? ORDER BY er.created_at DESC"
);
$incoming->bind_param('i', $userId);
$incoming->execute();
$incoming = $incoming->get_result()->fetch_all(MYSQLI_ASSOC);

$outgoing = $conn->prepare(
    "SELECT er.request_id, er.status, er.message, er.created_at,
            b.book_id, b.title AS book_title,
            u.username AS owner_name
     FROM exchange_requests er
     JOIN books b ON b.book_id = er.book_id
     JOIN users u ON u.user_id = er.owner_id
     WHERE er.requester_id = ? ORDER BY er.created_at DESC"
);
$outgoing->bind_param('i', $userId);
$outgoing->execute();
$outgoing = $outgoing->get_result()->fetch_all(MYSQLI_ASSOC);

$csrf      = csrfToken();
$activeTab = $_GET['tab'] ?? 'incoming';
require_once __DIR__ . '/includes/header.php';
?>

<div class="dashboard-page">
<div class="container">

    <div class="dash-head"><h1>Exchange Requests</h1></div>

    <div class="tab-bar">
        <button class="tab-btn <?php echo $activeTab === 'incoming' ? 'active' : ''; ?>"
                onclick="switchTab('incoming')">Incoming (<?php echo count($incoming); ?>)</button>
        <button class="tab-btn <?php echo $activeTab === 'outgoing' ? 'active' : ''; ?>"
                onclick="switchTab('outgoing')">Outgoing (<?php echo count($outgoing); ?>)</button>
    </div>

    <div class="tab-panel <?php echo $activeTab === 'incoming' ? 'active' : ''; ?>" id="tab-incoming">
        <?php if ($incoming): ?>
        <?php foreach ($incoming as $r): ?>
        <div class="request-card">
            <div>
                <div class="request-book">
                    <a href="<?php echo BASE_PATH; ?>/book.php?id=<?php echo $r['book_id']; ?>">
                        <?php echo e($r['book_title']); ?>
                    </a>
                </div>
                <div class="request-who">Requested by <strong><?php echo e($r['requester_name']); ?></strong></div>
                <span class="badge <?php echo statusClass($r['status']); ?>"><?php echo ucfirst($r['status']); ?></span>
                <?php if ($r['message']): ?>
                <div class="request-msg">"<?php echo e($r['message']); ?>"</div>
                <?php endif; ?>
                <div class="request-time"><?php echo timeAgo($r['created_at']); ?></div>
            </div>
            <div class="request-btns">
                <?php if ($r['status'] === 'pending'): ?>
                <form method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="request_id" value="<?php echo $r['request_id']; ?>">
                    <input type="hidden" name="action" value="accept">
                    <button type="submit" class="btn btn-success btn-sm btn-full">Accept</button>
                </form>
                <form method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="request_id" value="<?php echo $r['request_id']; ?>">
                    <input type="hidden" name="action" value="decline">
                    <button type="submit" class="btn btn-danger btn-sm btn-full">Decline</button>
                </form>
                <?php elseif ($r['status'] === 'accepted'): ?>
                <form method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="request_id" value="<?php echo $r['request_id']; ?>">
                    <input type="hidden" name="action" value="complete">
                    <button type="submit" class="btn btn-accent btn-sm btn-full">Mark Complete</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📬</div>
            <h3>No incoming requests</h3>
            <p>When someone requests one of your books, it will appear here.</p>
        </div>
        <?php endif; ?>
    </div>

    <div class="tab-panel <?php echo $activeTab === 'outgoing' ? 'active' : ''; ?>" id="tab-outgoing">
        <?php if ($outgoing): ?>
        <?php foreach ($outgoing as $r): ?>
        <div class="request-card">
            <div>
                <div class="request-book">
                    <a href="<?php echo BASE_PATH; ?>/book.php?id=<?php echo $r['book_id']; ?>">
                        <?php echo e($r['book_title']); ?>
                    </a>
                </div>
                <div class="request-who">Owner: <strong><?php echo e($r['owner_name']); ?></strong></div>
                <span class="badge <?php echo statusClass($r['status']); ?>"><?php echo ucfirst($r['status']); ?></span>
                <?php if ($r['message']): ?>
                <div class="request-msg">"<?php echo e($r['message']); ?>"</div>
                <?php endif; ?>
                <div class="request-time"><?php echo timeAgo($r['created_at']); ?></div>
            </div>
            <div class="request-btns">
                <?php if (in_array($r['status'], ['pending','accepted'])): ?>
                <form method="POST">
                    <?php echo csrfField(); ?>
                    <input type="hidden" name="request_id" value="<?php echo $r['request_id']; ?>">
                    <input type="hidden" name="action" value="cancel">
                    <button type="submit" class="btn btn-outline btn-sm btn-full">Cancel</button>
                </form>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📤</div>
            <h3>No outgoing requests</h3>
            <p>Browse books and send a request to get started.</p>
            <a href="<?php echo BASE_PATH; ?>/catalog.php" class="btn btn-accent">Browse Books</a>
        </div>
        <?php endif; ?>
    </div>

</div>
</div>

<script>
function switchTab(tab) {
    document.querySelectorAll('.tab-panel').forEach(function(p) { p.classList.remove('active'); });
    document.querySelectorAll('.tab-btn').forEach(function(b)   { b.classList.remove('active'); });
    document.getElementById('tab-' + tab).classList.add('active');
    event.target.classList.add('active');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

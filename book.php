<?php
$pageTitle = 'Book Detail';
$pageCss   = 'book';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
startSession();

$bookId = (int)($_GET['id'] ?? 0);
if ($bookId <= 0) {
    header('Location: ' . BASE_PATH . '/catalog.php'); exit;
}

$stmt = $conn->prepare(
    "SELECT b.*, u.username, u.first_name, u.last_name, u.location AS owner_location
     FROM   books b
     JOIN   users u ON u.user_id = b.user_id
     WHERE  b.book_id = ?"
);
$stmt->bind_param('i', $bookId);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$book) {
    header('Location: ' . BASE_PATH . '/catalog.php'); exit;
}

$pageTitle = e($book['title']);

$conn->query("UPDATE books SET views = views + 1 WHERE book_id = $bookId");

$rStmt = $conn->prepare("SELECT AVG(rating) AS avg, COUNT(*) AS cnt FROM reviews WHERE reviewee_id = ?");
$rStmt->bind_param('i', $book['user_id']);
$rStmt->execute();
$rating = $rStmt->get_result()->fetch_assoc();
$rStmt->close();

$requestError = '';
$requestOk    = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_request'])) {
    requireLogin();
    if (!csrfCheck()) {
        $requestError = 'Invalid form submission.';
    } elseif ((int)$_SESSION['user_id'] === (int)$book['user_id']) {
        $requestError = 'You cannot request your own book.';
    } elseif (!$book['is_available']) {
        $requestError = 'This book is no longer available.';
    } else {
        $dup = $conn->prepare(
            "SELECT request_id FROM exchange_requests
             WHERE book_id = ? AND requester_id = ? AND status = 'pending'"
        );
        $dup->bind_param('ii', $bookId, $_SESSION['user_id']);
        $dup->execute();
        if ($dup->get_result()->num_rows > 0) {
            $requestError = 'You already have a pending request for this book.';
        } else {
            $msg = trim($_POST['message'] ?? '');
            $ins = $conn->prepare(
                "INSERT INTO exchange_requests (book_id, requester_id, owner_id, message)
                 VALUES (?, ?, ?, ?)"
            );
            $ins->bind_param('iiis', $bookId, $_SESSION['user_id'], $book['user_id'], $msg);
            $ins->execute();
            $ins->close();

            createNotification(
                $conn, $book['user_id'], 'request',
                'New exchange request',
                $_SESSION['username'] . ' wants to exchange "' . $book['title'] . '".',
                BASE_PATH . '/requests.php'
            );

            $requestOk = true;
            setFlash('success', 'Request sent! The owner will be in touch.');
        }
        $dup->close();
    }
}

$sim = $conn->prepare(
    "SELECT book_id, title, author, cover_image, condition_status
     FROM books
     WHERE genre = ? AND book_id != ? AND is_available = 1
     ORDER BY RAND() LIMIT 4"
);
$sim->bind_param('si', $book['genre'], $bookId);
$sim->execute();
$similar = $sim->get_result()->fetch_all(MYSQLI_ASSOC);
$sim->close();

$csrf = csrfToken();
require_once __DIR__ . '/includes/header.php';
?>

<div class="container">

    <div class="breadcrumb" style="padding-top:28px;">
        <a href="<?php echo BASE_PATH; ?>/index.php">Home</a> ›
        <a href="<?php echo BASE_PATH; ?>/catalog.php">Browse</a> ›
        <?php echo e($book['title']); ?>
    </div>

    <div class="book-detail">

        <div class="book-detail-cover">
            <?php if ($book['cover_image']): ?>
            <img src="<?php echo e($book['cover_image']); ?>"
                 alt="Cover of <?php echo e($book['title']); ?>">
            <?php else: ?>
            <div class="cover-placeholder-lg">📖</div>
            <?php endif; ?>
        </div>

        <div class="book-detail-info">
            <div class="book-detail-genre"><?php echo e($book['genre']); ?></div>
            <h1 class="book-detail-title"><?php echo e($book['title']); ?></h1>
            <p class="book-detail-author">by <?php echo e($book['author']); ?></p>

            <div class="book-meta">
                <span class="book-meta-item">
                    <strong>Condition:</strong>
                    <span class="badge <?php echo conditionClass($book['condition_status']); ?>">
                        <?php echo e($book['condition_status']); ?>
                    </span>
                </span>
                <span class="book-meta-item">
                    <strong>Listed:</strong> <?php echo date('d M Y', strtotime($book['listed_at'])); ?>
                </span>
                <span class="book-meta-item">
                    <strong>Views:</strong> <?php echo number_format($book['views']); ?>
                </span>
            </div>

            <?php if ($book['description']): ?>
            <p class="book-description"><?php echo e($book['description']); ?></p>
            <?php endif; ?>

            <div class="owner-card">
                <div class="owner-label">Listed by</div>
                <div class="owner-name"><?php echo e($book['first_name'] . ' ' . $book['last_name']); ?></div>
                <div class="owner-meta">
                    @<?php echo e($book['username']); ?>
                    <?php if ($book['owner_location']): ?>
                    · <?php echo e($book['owner_location']); ?>
                    <?php endif; ?>
                </div>
                <?php if ($rating['cnt'] > 0): ?>
                <div class="owner-stars" style="margin-top:6px;">
                    <?php echo renderStars($rating['avg']); ?>
                    <small style="color:#78716C;">(<?php echo $rating['cnt']; ?> review<?php echo $rating['cnt'] !== 1 ? 's' : ''; ?>)</small>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($book['is_available'] && isLoggedIn() && (int)$_SESSION['user_id'] !== (int)$book['user_id']): ?>
            <div class="request-box" id="request">
                <h3>Request This Book</h3>

                <?php if ($requestOk): ?>
                <div class="alert alert-success">Your request has been sent!</div>
                <?php elseif ($requestError): ?>
                <div class="alert alert-error"><?php echo e($requestError); ?></div>
                <?php endif; ?>

                <?php if (!$requestOk): ?>
                <form method="POST" action="">
                    <input type="hidden" name="csrf_token"   value="<?php echo $csrf; ?>">
                    <input type="hidden" name="send_request" value="1">
                    <div class="form-group">
                        <label class="form-label" for="message">
                            Message to owner <span class="req">*</span>
                        </label>
                        <textarea id="message" name="message" class="form-control"
                                  placeholder="Introduce yourself and mention what you might offer in return…"
                                  required minlength="10" maxlength="500"></textarea>
                        <p class="form-hint">10–500 characters</p>
                    </div>
                    <button type="submit" class="btn btn-accent btn-lg">Send Request</button>
                </form>
                <?php endif; ?>
            </div>

            <?php elseif (!isLoggedIn()): ?>
            <div class="request-box">
                <p style="color:#57534E;">
                    <a href="<?php echo BASE_PATH; ?>/login.php">Log in</a> or
                    <a href="<?php echo BASE_PATH; ?>/register.php">create an account</a>
                    to request this book.
                </p>
            </div>
            <?php elseif (!$book['is_available']): ?>
            <div class="alert alert-info">This book is no longer available.</div>
            <?php endif; ?>

        </div>
    </div>

    <?php if ($similar): ?>
    <div class="similar-section">
        <h2>More <?php echo e($book['genre']); ?> Books</h2>
        <div class="books-grid">
            <?php foreach ($similar as $s): ?>
            <div class="book-card">
                <div class="book-cover-wrap">
                    <?php if ($s['cover_image']): ?>
                    <img src="<?php echo e($s['cover_image']); ?>"
                         alt="<?php echo e($s['title']); ?>" loading="lazy">
                    <?php else: ?>
                    <div class="book-cover-placeholder"><span>📖</span></div>
                    <?php endif; ?>
                    <span class="badge <?php echo conditionClass($s['condition_status']); ?>">
                        <?php echo e($s['condition_status']); ?>
                    </span>
                </div>
                <div class="book-body">
                    <div class="book-title"><?php echo e($s['title']); ?></div>
                    <div class="book-author"><?php echo e($s['author']); ?></div>
                    <div class="book-foot">
                        <a href="<?php echo BASE_PATH; ?>/book.php?id=<?php echo $s['book_id']; ?>"
                           class="btn btn-dark btn-sm">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
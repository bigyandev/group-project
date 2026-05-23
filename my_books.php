<?php
// ============================================================
//  my_books.php  –  Manage your own book listings
// ============================================================
$pageTitle = 'My Books';
$pageCss   = 'dashboard';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
startSession();
requireLogin();

$userId = $_SESSION['user_id'];

// ── Handle POST actions (toggle availability / delete) ────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfCheck()) {
    $bookId = (int)($_POST['book_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    if ($action === 'toggle') {
        $conn->query("UPDATE books SET is_available = NOT is_available
                      WHERE book_id = $bookId AND user_id = $userId");
        setFlash('success', 'Availability updated.');
    } elseif ($action === 'delete') {
        $del = $conn->prepare("DELETE FROM books WHERE book_id = ? AND user_id = ?");
        $del->bind_param('ii', $bookId, $userId);
        $del->execute();
        $del->close();
        setFlash('success', 'Book removed from your listings.');
    }
    header('Location: ' . BASE_PATH . '/my_books.php'); exit;;
}

// ── Fetch user's books ────────────────────────────────────────
$books = $conn->prepare(
    "SELECT book_id, title, author, genre, condition_status, cover_image,
            is_available, views, listed_at
     FROM   books
     WHERE  user_id = ?
     ORDER  BY listed_at DESC"
);
$books->bind_param('i', $userId);
$books->execute();
$books = $books->get_result()->fetch_all(MYSQLI_ASSOC);

$csrf = csrfToken();
require_once __DIR__ . '/includes/header.php';
?>

<div class="dashboard-page">
<div class="container">

    <div class="dash-head">
        <h1>My Books</h1>
        <a href="<?php echo BASE_PATH; ?>/add_book.php" class="btn btn-accent">+ Add a Book</a>
    </div>

    <?php if ($books): ?>

    <?php foreach ($books as $b): ?>
    <div class="listing-row">

        <!-- Thumbnail -->
        <?php if ($b['cover_image']): ?>
        <img src="<?php echo e($b['cover_image']); ?>"
             alt="<?php echo e($b['title']); ?>"
             class="listing-thumb">
        <?php else: ?>
        <div class="listing-thumb-empty">📖</div>
        <?php endif; ?>

        <!-- Info -->
        <div>
            <div class="listing-title"><?php echo e($b['title']); ?></div>
            <div class="listing-author">by <?php echo e($b['author']); ?></div>
            <div class="listing-meta">
                <span class="badge <?php echo conditionClass($b['condition_status']); ?>">
                    <?php echo e($b['condition_status']); ?>
                </span>
                &nbsp;
                <?php if ($b['is_available']): ?>
                <span class="badge badge-green">Available</span>
                <?php else: ?>
                <span class="badge badge-gray">Unavailable</span>
                <?php endif; ?>
                &nbsp;
                <?php echo number_format($b['views']); ?> views
                · Listed <?php echo timeAgo($b['listed_at']); ?>
            </div>
        </div>

        <!-- Action buttons -->
        <div class="listing-btns">
            <a href="<?php echo BASE_PATH; ?>/book.php?id=<?php echo $b['book_id']; ?>"
               class="btn btn-outline btn-sm">View</a>

            <a href="<?php echo BASE_PATH; ?>/edit_book.php?id=<?php echo $b['book_id']; ?>"
               class="btn btn-dark btn-sm">Edit</a>

            <!-- Toggle availability -->
            <form method="POST" action="" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                <input type="hidden" name="book_id"    value="<?php echo $b['book_id']; ?>">
                <input type="hidden" name="action"     value="toggle">
                <button type="submit" class="btn btn-warning btn-sm">
                    <?php echo $b['is_available'] ? 'Mark Unavailable' : 'Mark Available'; ?>
                </button>
            </form>

            <!-- Delete -->
            <form method="POST" action="" style="display:inline;"
                  onsubmit="return confirm('Delete this book? This cannot be undone.');">
                <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
                <input type="hidden" name="book_id"    value="<?php echo $b['book_id']; ?>">
                <input type="hidden" name="action"     value="delete">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
            </form>
        </div>

    </div>
    <?php endforeach; ?>

    <?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">📦</div>
        <h3>No books listed yet</h3>
        <p>Add your first book and let others request it.</p>
        <a href="<?php echo BASE_PATH; ?>/add_book.php" class="btn btn-accent">Add a Book</a>
    </div>
    <?php endif; ?>

</div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

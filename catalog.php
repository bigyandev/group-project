<?php
// ============================================================
//  catalog.php  –  Browse all available books
// ============================================================
$pageTitle = 'Browse Books';
$pageCss   = 'catalog';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
startSession();

// ── Filter inputs from URL ────────────────────────────────────
$search    = trim($_GET['search'] ?? '');
$genre     = trim($_GET['genre']  ?? '');
$condition = trim($_GET['condition'] ?? '');
$sort      = $_GET['sort'] ?? 'newest';
$page      = max(1, (int)($_GET['page'] ?? 1));
$perPage   = 12;
$offset    = ($page - 1) * $perPage;

// ── Build query with filters ──────────────────────────────────
$where  = ['b.is_available = 1'];
$params = [];
$types  = '';

if ($search !== '') {
    $where[]  = '(b.title LIKE ? OR b.author LIKE ?)';
    $like     = '%' . $search . '%';
    $params[] = $like;
    $params[] = $like;
    $types   .= 'ss';
}
if ($genre !== '') {
    $where[]  = 'b.genre = ?';
    $params[] = $genre;
    $types   .= 's';
}
if ($condition !== '') {
    $where[]  = 'b.condition_status = ?';
    $params[] = $condition;
    $types   .= 's';
}

$whereSQL = 'WHERE ' . implode(' AND ', $where);

// Sort options
$orderSQL = match($sort) {
    'oldest'  => 'b.listed_at ASC',
    'title'   => 'b.title ASC',
    default   => 'b.listed_at DESC',
};

// Total count for pagination
$countSQL = "SELECT COUNT(*) FROM books b $whereSQL";
$countStmt = $conn->prepare($countSQL);
if ($params) $countStmt->bind_param($types, ...$params);
$countStmt->execute();
$countStmt->bind_result($totalCount);
$countStmt->fetch();
$countStmt->close();
$totalPages = max(1, (int)ceil($totalCount / $perPage));

// Fetch books for this page
$sql  = "SELECT b.book_id, b.title, b.author, b.genre, b.condition_status, b.cover_image, u.username
         FROM   books b
         JOIN   users u ON u.user_id = b.user_id
         $whereSQL
         ORDER  BY $orderSQL
         LIMIT  ? OFFSET ?";
$stmt = $conn->prepare($sql);
$allParams = array_merge($params, [$perPage, $offset]);
$allTypes  = $types . 'ii';
$stmt->bind_param($allTypes, ...$allParams);
$stmt->execute();
$books = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Build base URL for pagination links
$baseUrl = BASE_PATH . '/catalog.php?' . http_build_query(array_filter([
    'search' => $search, 'genre' => $genre, 'condition' => $condition, 'sort' => $sort
]));

$genres     = ['Fiction','Non-Fiction','Science Fiction','Mystery','Fantasy','Romance',
               'Biography','History','Science','Self-Help','Children','Other'];
$conditions = ['New','Like New','Good','Fair','Poor'];

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Filter Bar ─────────────────────────────────────── -->
<div class="filter-bar">
    <div class="container">
        <form class="filter-form" method="GET" action="">

            <!-- Search -->
            <div class="search-join">
                <input type="text" name="search" class="form-control"
                       placeholder="Search title or author…"
                       value="<?php echo e($search); ?>">
                <button type="submit" class="btn btn-dark">Search</button>
            </div>

            <!-- Genre -->
            <div class="filter-field">
                <label for="genre">Genre</label>
                <select name="genre" id="genre" class="form-control" onchange="this.form.submit()">
                    <option value="">All Genres</option>
                    <?php foreach ($genres as $g): ?>
                    <option value="<?php echo e($g); ?>" <?php echo $genre === $g ? 'selected' : ''; ?>>
                        <?php echo e($g); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Condition -->
            <div class="filter-field">
                <label for="condition">Condition</label>
                <select name="condition" id="condition" class="form-control" onchange="this.form.submit()">
                    <option value="">Any Condition</option>
                    <?php foreach ($conditions as $c): ?>
                    <option value="<?php echo e($c); ?>" <?php echo $condition === $c ? 'selected' : ''; ?>>
                        <?php echo e($c); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Sort -->
            <div class="filter-field">
                <label for="sort">Sort By</label>
                <select name="sort" id="sort" class="form-control" onchange="this.form.submit()">
                    <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Newest First</option>
                    <option value="oldest" <?php echo $sort === 'oldest' ? 'selected' : ''; ?>>Oldest First</option>
                    <option value="title"  <?php echo $sort === 'title'  ? 'selected' : ''; ?>>Title A–Z</option>
                </select>
            </div>

        </form>
    </div>
</div>

<!-- ── Results ────────────────────────────────────────── -->
<div class="container" style="padding-top:36px;padding-bottom:60px;">

    <div class="results-info">
        <span class="results-count">
            <?php echo number_format($totalCount); ?> book<?php echo $totalCount !== 1 ? 's' : ''; ?> found
            <?php if ($search || $genre || $condition): ?>
            — <a href="<?php echo BASE_PATH; ?>/catalog.php">Clear filters</a>
            <?php endif; ?>
        </span>
        <span class="results-count">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span>
    </div>

    <?php if ($books): ?>
    <div class="books-grid">
        <?php foreach ($books as $b): ?>
        <div class="book-card">
            <div class="book-cover-wrap">
                <?php if ($b['cover_image']): ?>
                <img src="<?php echo e($b['cover_image']); ?>"
                     alt="Cover of <?php echo e($b['title']); ?>"
                     loading="lazy">
                <?php else: ?>
                <div class="book-cover-placeholder"><span>📖</span></div>
                <?php endif; ?>
                <span class="badge <?php echo conditionClass($b['condition_status']); ?>">
                    <?php echo e($b['condition_status']); ?>
                </span>
            </div>
            <div class="book-body">
                <div class="book-title"><?php echo e($b['title']); ?></div>
                <div class="book-author">by <?php echo e($b['author']); ?></div>
                <div class="book-genre"><?php echo e($b['genre']); ?></div>
                <div class="book-foot">
                    <a href="<?php echo BASE_PATH; ?>/book.php?id=<?php echo $b['book_id']; ?>"
                       class="btn btn-dark btn-sm">View</a>
                    <?php if (isLoggedIn()): ?>
                    <a href="<?php echo BASE_PATH; ?>/book.php?id=<?php echo $b['book_id']; ?>#request"
                       class="btn btn-accent btn-sm">Request</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
        <a href="<?php echo $baseUrl . '&page=' . ($page - 1); ?>" class="page-btn">← Prev</a>
        <?php else: ?>
        <span class="page-btn disabled">← Prev</span>
        <?php endif; ?>

        <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
        <a href="<?php echo $baseUrl . '&page=' . $p; ?>"
           class="page-btn <?php echo $p === $page ? 'active' : ''; ?>"><?php echo $p; ?></a>
        <?php endfor; ?>

        <?php if ($page < $totalPages): ?>
        <a href="<?php echo $baseUrl . '&page=' . ($page + 1); ?>" class="page-btn">Next →</a>
        <?php else: ?>
        <span class="page-btn disabled">Next →</span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <?php else: ?>
    <div class="empty-state">
        <div class="empty-icon">🔍</div>
        <h3>No books found</h3>
        <p>Try adjusting your search or filters.</p>
        <a href="<?php echo BASE_PATH; ?>/catalog.php" class="btn btn-accent">Clear Filters</a>
    </div>
    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

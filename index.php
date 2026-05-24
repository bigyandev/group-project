<?php
// ============================================================
//  index.php  –  BookLoop Homepage
// ============================================================
$pageTitle = 'Home';
$pageCss   = 'home';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
startSession();

// ── Fetch recent available books ──────────────────────────────
$recent = $conn->query(
    "SELECT b.book_id, b.title, b.author, b.genre, b.condition_status, b.cover_image,
            u.username
     FROM   books b
     JOIN   users u ON u.user_id = b.user_id
     WHERE  b.is_available = 1
     ORDER  BY b.listed_at DESC
     LIMIT  8"
)->fetch_all(MYSQLI_ASSOC);

// ── Site stats ────────────────────────────────────────────────
$totalBooks = $conn->query("SELECT COUNT(*) FROM books WHERE is_available = 1")->fetch_row()[0];
$totalUsers = $conn->query("SELECT COUNT(*) FROM users WHERE is_active = 1 AND is_admin = 0")->fetch_row()[0];
$totalSwaps = $conn->query("SELECT COUNT(*) FROM exchange_requests WHERE status = 'completed'")->fetch_row()[0];

require_once __DIR__ . '/includes/header.php';
?>

<!-- ── Hero Slideshow ──────────────────────────────────── -->
<section class="slideshow" aria-label="Featured books">

    <!-- Slide 1 -->
    <div class="slide active">
        <div class="slide-bg" style="background-image:url('https://images.unsplash.com/photo-1507842217343-583bb7270b66?w=1600')"></div>
        <div class="slide-content">
            <span class="slide-tag">Welcome to BookLoop</span>
            <h1 class="slide-title">Give Books a Second Life</h1>
            <p class="slide-text">Connect with readers nearby and swap books for free. Your next favourite read is waiting.</p>
            <div class="slide-actions">
                <a href="<?php echo BASE_PATH; ?>/catalog.php" class="btn btn-accent btn-lg">Browse Books</a>
                <a href="<?php echo BASE_PATH; ?>/register.php" class="btn btn-ghost btn-lg">Join Free</a>
            </div>
        </div>
    </div>

    <!-- Slide 2 -->
    <div class="slide">
        <div class="slide-bg" style="background-image:url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?w=1600')"></div>
        <div class="slide-content">
            <span class="slide-tag">Science Fiction</span>
            <h1 class="slide-title">Explore Other Worlds</h1>
            <p class="slide-text">Hundreds of sci-fi, fantasy and adventure titles listed by readers just like you.</p>
            <div class="slide-actions">
                <a href="<?php echo BASE_PATH; ?>/catalog.php?genre=Science+Fiction" class="btn btn-accent btn-lg">See Sci-Fi Books</a>
            </div>
        </div>
    </div>

    <!-- Slide 3 -->
    <div class="slide">
        <div class="slide-bg" style="background-image:url('https://images.unsplash.com/photo-1524995997946-a1c2e315a42f?w=1600')"></div>
        <div class="slide-content">
            <span class="slide-tag">Community</span>
            <h1 class="slide-title">Readers Helping Readers</h1>
            <p class="slide-text">A community built on a love of books — swap, discover and connect with fellow readers.</p>
            <div class="slide-actions">
                <a href="<?php echo BASE_PATH; ?>/register.php" class="btn btn-accent btn-lg">Get Started</a>
            </div>
        </div>
    </div>

    <!-- Slide 4 -->
    <div class="slide">
        <div class="slide-bg" style="background-image:url('https://images.unsplash.com/photo-1543002588-bfa74002ed7e?w=1600')"></div>
        <div class="slide-content">
            <span class="slide-tag">Add Your Books</span>
            <h1 class="slide-title">Clear Your Shelf</h1>
            <p class="slide-text">List books you no longer need and let someone else enjoy them while you find something new.</p>
            <div class="slide-actions">
                <a href="<?php echo BASE_PATH; ?>/add_book.php" class="btn btn-accent btn-lg">List a Book</a>
            </div>
        </div>
    </div>

    <!-- Slide 5 -->
    <div class="slide">
        <div class="slide-bg" style="background-image:url('https://images.unsplash.com/photo-1456513080510-7bf3a84b82f8?w=1600')"></div>
        <div class="slide-content">
            <span class="slide-tag">Every Genre</span>
            <h1 class="slide-title">Something for Every Reader</h1>
            <p class="slide-text">Fiction, non-fiction, biography, mystery, fantasy and more — all free to request.</p>
            <div class="slide-actions">
                <a href="<?php echo BASE_PATH; ?>/catalog.php" class="btn btn-accent btn-lg">Start Browsing</a>
            </div>
        </div>
    </div>

    <!-- Controls -->
    <button class="slide-arrow prev" id="slidePrev" aria-label="Previous slide">&#8592;</button>
    <button class="slide-arrow next" id="slideNext" aria-label="Next slide">&#8594;</button>

    <div class="slide-dots">
        <button class="dot active" aria-label="Slide 1"></button>
        <button class="dot" aria-label="Slide 2"></button>
        <button class="dot" aria-label="Slide 3"></button>
        <button class="dot" aria-label="Slide 4"></button>
        <button class="dot" aria-label="Slide 5"></button>
    </div>

</section>

<!-- ── Stats Strip ─────────────────────────────────────── -->
<div class="stats-strip">
    <div class="container stats-row">
        <div>
            <div class="stat-num"><?php echo number_format($totalBooks); ?>+</div>
            <div class="stat-label">Books Available</div>
        </div>
        <div>
            <div class="stat-num"><?php echo number_format($totalUsers); ?>+</div>
            <div class="stat-label">Readers</div>
        </div>
        <div>
            <div class="stat-num"><?php echo number_format($totalSwaps); ?>+</div>
            <div class="stat-label">Swaps Completed</div>
        </div>
        <div>
            <div class="stat-num">Free</div>
            <div class="stat-label">Always</div>
        </div>
    </div>
</div>

<!-- ── Recently Listed Books ──────────────────────────── -->
<section class="section">
    <div class="container">

        <!-- Genre quick-filter pills -->
        <div class="genre-pills">
            <a href="<?php echo BASE_PATH; ?>/catalog.php" class="genre-pill active">All</a>
            <?php
            $genres = ['Fiction','Non-Fiction','Science Fiction','Mystery','Fantasy','Biography','Self-Help'];
            foreach ($genres as $g):
            ?>
            <a href="<?php echo BASE_PATH; ?>/catalog.php?genre=<?php echo urlencode($g); ?>"
               class="genre-pill"><?php echo e($g); ?></a>
            <?php endforeach; ?>
        </div>

        <div class="section-head">
            <div>
                <h2 class="section-title">Recently Listed</h2>
                <p class="section-sub">Fresh additions from our community</p>
            </div>
            <a href="<?php echo BASE_PATH; ?>/catalog.php" class="btn btn-outline">View All Books</a>
        </div>

        <?php if ($recent): ?>
        <div class="books-grid">
            <?php foreach ($recent as $b): ?>
            <div class="book-card">
                <div class="book-cover-wrap">
                    <?php if ($b['cover_image']): ?>
                    <img src="<?php echo e($b['cover_image']); ?>"
                         alt="Cover of <?php echo e($b['title']); ?>"
                         loading="lazy">
                    <?php else: ?>
                    <div class="book-cover-placeholder">
                        <span>📖</span><p>No cover</p>
                    </div>
                    <?php endif; ?>
                    <span class="badge <?php echo conditionClass($b['condition_status']); ?>">
                        <?php echo e($b['condition_status']); ?>
                    </span>
                </div>
                <div class="book-body">
                    <div class="book-title"><?php echo e($b['title']); ?></div>
                    <div class="book-author"><?php echo e($b['author']); ?></div>
                    <div class="book-genre"><?php echo e($b['genre']); ?></div>
                    <div class="book-foot">
                        <a href="<?php echo BASE_PATH; ?>/book.php?id=<?php echo $b['book_id']; ?>"
                           class="btn btn-dark btn-sm">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📚</div>
            <h3>No books yet</h3>
            <p>Be the first to list a book!</p>
            <a href="<?php echo BASE_PATH; ?>/add_book.php" class="btn btn-accent">Add a Book</a>
        </div>
        <?php endif; ?>

    </div>
</section>

<!-- ── How It Works ───────────────────────────────────── -->
<section class="section steps-wrap">
    <div class="container">
        <h2 class="section-title" style="color:#fff;text-align:center;margin-bottom:8px;">How BookLoop Works</h2>
        <p class="section-sub" style="color:rgba(255,255,255,.55);text-align:center;margin-bottom:48px;">Three simple steps to your next great read</p>
        <div class="steps-grid">
            <div class="step-card">
                <div class="step-num">1</div>
                <div class="step-icon">📝</div>
                <h3>List Your Books</h3>
                <p>Add the books sitting on your shelf. Upload a cover photo and a short description.</p>
            </div>
            <div class="step-card">
                <div class="step-num">2</div>
                <div class="step-icon">🔍</div>
                <h3>Browse &amp; Request</h3>
                <p>Search by title, author or genre. Send a swap request to the owner with a message.</p>
            </div>
            <div class="step-card">
                <div class="step-num">3</div>
                <div class="step-icon">🤝</div>
                <h3>Swap &amp; Enjoy</h3>
                <p>The owner accepts, you arrange the handover, and you both enjoy new books for free.</p>
            </div>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

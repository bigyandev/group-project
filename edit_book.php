<?php
$pageTitle = 'Edit Book';
$pageCss   = 'form';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
startSession();
requireLogin();

$bookId = (int)($_GET['id'] ?? 0);
if ($bookId <= 0) { header('Location: ' . BASE_PATH . '/my_books.php'); exit; }

$stmt = $conn->prepare("SELECT * FROM books WHERE book_id = ? AND user_id = ?");
$stmt->bind_param('ii', $bookId, $_SESSION['user_id']);
$stmt->execute();
$book = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$book) { header('Location: ' . BASE_PATH . '/my_books.php'); exit; }

$error      = '';
$genres     = ['Fiction','Non-Fiction','Science Fiction','Mystery','Fantasy','Romance',
               'Biography','History','Science','Self-Help','Children','Other'];
$conditions = ['New','Like New','Good','Fair','Poor'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfCheck()) {
        $error = 'Invalid form submission.';
    } else {
        $title     = trim($_POST['title']       ?? '');
        $author    = trim($_POST['author']      ?? '');
        $genre     = trim($_POST['genre']       ?? '');
        $condition = trim($_POST['condition']   ?? '');
        $desc      = trim($_POST['description'] ?? '');
        $available = isset($_POST['is_available']) ? 1 : 0;
        $coverUrl  = $book['cover_image'];

        if (!empty($_FILES['cover']['name'])) {
            $ext     = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (!in_array($ext, $allowed)) {
                $error = 'Cover must be JPG, PNG, GIF or WebP.';
            } elseif ($_FILES['cover']['size'] > 5 * 1024 * 1024) {
                $error = 'Cover must be under 5 MB.';
            } else {
                $filename = uniqid('cover_') . '.' . $ext;
                if (!is_dir(COVERS_DIR)) mkdir(COVERS_DIR, 0755, true);
                if (move_uploaded_file($_FILES['cover']['tmp_name'], COVERS_DIR . $filename)) {
                    $coverUrl = COVERS_URL . $filename;
                } else {
                    $error = 'Failed to save cover image.';
                }
            }
        }

        if (!$error) {
            $upd = $conn->prepare(
                "UPDATE books SET title=?, author=?, genre=?, description=?,
                 condition_status=?, cover_image=?, is_available=?
                 WHERE book_id=? AND user_id=?"
            );
            $upd->bind_param('sssssssii',
                $title, $author, $genre, $desc,
                $condition, $coverUrl, $available,
                $bookId, $_SESSION['user_id']
            );
            $upd->execute();
            $upd->close();

            setFlash('success', 'Book updated successfully.');
            header('Location: ' . BASE_PATH . '/book.php?id=' . $bookId);
            exit;
        }
    }
}

$csrf = csrfToken();
require_once __DIR__ . '/includes/header.php';
?>

<div class="form-page">
<div class="container">
<div class="form-card">
    <h2>Edit Book</h2>
    <p class="form-sub">Update the details for "<?php echo e($book['title']); ?>"</p>

    <?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <?php echo csrfField(); ?>

        <div class="form-group">
            <label class="form-label" for="title">Title <span class="req">*</span></label>
            <input type="text" id="title" name="title" class="form-control"
                   required maxlength="200" value="<?php echo e($_POST['title'] ?? $book['title']); ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="author">Author <span class="req">*</span></label>
            <input type="text" id="author" name="author" class="form-control"
                   required maxlength="150" value="<?php echo e($_POST['author'] ?? $book['author']); ?>">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="genre">Genre <span class="req">*</span></label>
                <select id="genre" name="genre" class="form-control" required>
                    <?php foreach ($genres as $g): ?>
                    <option value="<?php echo e($g); ?>" <?php echo $g === $book['genre'] ? 'selected' : ''; ?>>
                        <?php echo e($g); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="condition">Condition <span class="req">*</span></label>
                <select id="condition" name="condition" class="form-control" required>
                    <?php foreach ($conditions as $c): ?>
                    <option value="<?php echo e($c); ?>" <?php echo $c === $book['condition_status'] ? 'selected' : ''; ?>>
                        <?php echo e($c); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" name="description" class="form-control"
                      maxlength="2000"><?php echo e($_POST['description'] ?? $book['description']); ?></textarea>
        </div>
        <div class="form-group" style="display:flex;align-items:center;gap:10px;">
            <input type="checkbox" id="is_available" name="is_available" value="1"
                   <?php echo $book['is_available'] ? 'checked' : ''; ?>
                   style="width:18px;height:18px;cursor:pointer;">
            <label for="is_available" class="form-label" style="margin:0;cursor:pointer;">
                Available for exchange
            </label>
        </div>

        <hr class="form-divider">
        <p class="form-section-title">Cover Image</p>

        <?php if ($book['cover_image']): ?>
        <img src="<?php echo e($book['cover_image']); ?>" alt="Current cover" class="image-preview">
        <p class="form-hint" style="margin-bottom:12px;">Upload a new image to replace it</p>
        <?php else: ?>
        <div class="image-preview-empty">📖</div>
        <?php endif; ?>

        <div class="form-group">
            <label class="form-label" for="cover">New Cover Image</label>
            <input type="file" id="cover" name="cover" class="form-control"
                   accept="image/jpeg,image/png,image/gif,image/webp">
            <p class="form-hint">JPG, PNG, GIF or WebP · Max 5 MB</p>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;">
            <button type="submit" class="btn btn-accent btn-lg">Save Changes</button>
            <a href="<?php echo BASE_PATH; ?>/book.php?id=<?php echo $bookId; ?>"
               class="btn btn-outline btn-lg">Cancel</a>
        </div>
    </form>
</div>
</div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

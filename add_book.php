<?php
$pageTitle = 'Add a Book';
$pageCss   = 'form';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
startSession();
requireLogin();

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
        $userId    = $_SESSION['user_id'];
        $coverUrl  = null;

        if (!empty($_FILES['cover']['name'])) {
            $ext     = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg','jpeg','png','gif','webp'];
            if (!in_array($ext, $allowed)) {
                $error = 'Cover must be JPG, PNG, GIF or WebP.';
            } elseif ($_FILES['cover']['size'] > 5 * 1024 * 1024) {
                $error = 'Cover image must be under 5 MB.';
            } else {
                $filename = uniqid('cover_') . '.' . $ext;
                if (!is_dir(COVERS_DIR)) mkdir(COVERS_DIR, 0755, true);
                if (move_uploaded_file($_FILES['cover']['tmp_name'], COVERS_DIR . $filename)) {
                    $coverUrl = COVERS_URL . $filename;
                } else {
                    $error = 'Failed to save cover image. Check folder permissions.';
                }
            }
        }

        if (!$error) {
            $stmt = $conn->prepare(
                "INSERT INTO books (user_id, title, author, genre, description, condition_status, cover_image)
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
            $stmt->bind_param('issssss', $userId, $title, $author, $genre, $desc, $condition, $coverUrl);
            $stmt->execute();
            $newId = $stmt->insert_id;
            $stmt->close();

            setFlash('success', '"' . $title . '" has been listed!');
            header('Location: ' . BASE_PATH . '/book.php?id=' . $newId);
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
    <h2>List a Book</h2>
    <p class="form-sub">Fill in the details below to make your book available for swapping.</p>

    <?php if ($error): ?>
    <div class="alert alert-error"><?php echo e($error); ?></div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <?php echo csrfField(); ?>

        <p class="form-section-title">Book Information</p>

        <div class="form-group">
            <label class="form-label" for="title">Title <span class="req">*</span></label>
            <input type="text" id="title" name="title" class="form-control"
                   placeholder="e.g. The Great Gatsby" required maxlength="200"
                   value="<?php echo e($_POST['title'] ?? ''); ?>">
        </div>
        <div class="form-group">
            <label class="form-label" for="author">Author <span class="req">*</span></label>
            <input type="text" id="author" name="author" class="form-control"
                   placeholder="e.g. F. Scott Fitzgerald" required maxlength="150"
                   value="<?php echo e($_POST['author'] ?? ''); ?>">
        </div>
        <div class="form-row">
            <div class="form-group">
                <label class="form-label" for="genre">Genre <span class="req">*</span></label>
                <select id="genre" name="genre" class="form-control" required>
                    <option value="">Select genre…</option>
                    <?php foreach ($genres as $g): ?>
                    <option value="<?php echo e($g); ?>" <?php echo ($_POST['genre'] ?? '') === $g ? 'selected' : ''; ?>>
                        <?php echo e($g); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="condition">Condition <span class="req">*</span></label>
                <select id="condition" name="condition" class="form-control" required>
                    <option value="">Select condition…</option>
                    <?php foreach ($conditions as $c): ?>
                    <option value="<?php echo e($c); ?>" <?php echo ($_POST['condition'] ?? '') === $c ? 'selected' : ''; ?>>
                        <?php echo e($c); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="description">Description</label>
            <textarea id="description" name="description" class="form-control"
                      placeholder="A short summary of the book…" maxlength="2000"><?php echo e($_POST['description'] ?? ''); ?></textarea>
        </div>

        <hr class="form-divider">
        <p class="form-section-title">Cover Image (optional)</p>

        <div class="form-group">
            <label class="form-label" for="cover">Upload Cover Photo</label>
            <input type="file" id="cover" name="cover" class="form-control"
                   accept="image/jpeg,image/png,image/gif,image/webp">
            <p class="form-hint">JPG, PNG, GIF or WebP · Max 5 MB</p>
        </div>

        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px;">
            <button type="submit" class="btn btn-accent btn-lg">List Book</button>
            <a href="<?php echo BASE_PATH; ?>/my_books.php" class="btn btn-outline btn-lg">Cancel</a>
        </div>
    </form>
</div>
</div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

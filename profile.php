<?php
// ============================================================
//  profile.php  –  View & Edit user profile
// ============================================================
$pageTitle = 'My Profile';
$pageCss   = 'profile';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
startSession();
requireLogin();

$userId = $_SESSION['user_id'];
$error  = '';

// ── Load user ─────────────────────────────────────────────────
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// ── Stats ─────────────────────────────────────────────────────
$bookCount = $conn->query("SELECT COUNT(*) FROM books WHERE user_id = $userId")->fetch_row()[0];
$swapCount = $conn->query(
    "SELECT COUNT(*) FROM exchange_requests
     WHERE (owner_id = $userId OR requester_id = $userId) AND status = 'completed'"
)->fetch_row()[0];

// ── Handle profile update ─────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrfCheck()) {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName  = trim($_POST['last_name']  ?? '');
    $location  = trim($_POST['location']   ?? '');
    $bio       = trim($_POST['bio']        ?? '');
    $avatarUrl = $user['avatar'];

    // Handle avatar upload
    if (!empty($_FILES['avatar']['name'])) {
        $ext     = strtolower(pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','gif','webp'];
        if (!in_array($ext, $allowed)) {
            $error = 'Avatar must be JPG, PNG, GIF or WebP.';
        } elseif ($_FILES['avatar']['size'] > 2 * 1024 * 1024) {
            $error = 'Avatar must be under 2 MB.';
        } else {
            $filename = 'avatar_' . $userId . '_' . time() . '.' . $ext;
            if (!is_dir(AVATARS_DIR)) mkdir(AVATARS_DIR, 0755, true);
            if (move_uploaded_file($_FILES['avatar']['tmp_name'], AVATARS_DIR . $filename)) {
                $avatarUrl = AVATARS_URL . $filename;
            }
        }
    }

    if (!$error) {
        $upd = $conn->prepare(
            "UPDATE users SET first_name = ?, last_name = ?, location = ?, bio = ?, avatar = ?
             WHERE user_id = ?"
        );
        $upd->bind_param('sssssi', $firstName, $lastName, $location, $bio, $avatarUrl, $userId);
        $upd->execute();
        $upd->close();

        // Refresh user data
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->bind_param('i', $userId);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        setFlash('success', 'Profile updated.');
    }
}

$csrf = csrfToken();
require_once __DIR__ . '/includes/header.php';
?>

<div class="profile-page">
<div class="container">
<div class="profile-layout">

    <!-- ── Left: profile card ─────────────────────────────── -->
    <div class="profile-card">

        <?php if ($user['avatar']): ?>
        <img src="<?php echo e($user['avatar']); ?>"
             alt="Avatar" class="profile-avatar">
        <?php else: ?>
        <div class="profile-initials">
            <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
        </div>
        <?php endif; ?>

        <div class="profile-name"><?php echo e($user['first_name'] . ' ' . $user['last_name']); ?></div>
        <div class="profile-username">@<?php echo e($user['username']); ?></div>
        <?php if ($user['location']): ?>
        <div class="profile-location">📍 <?php echo e($user['location']); ?></div>
        <?php endif; ?>

        <div class="profile-stats">
            <div class="pstat">
                <div class="pstat-val"><?php echo $bookCount; ?></div>
                <div class="pstat-lbl">Books</div>
            </div>
            <div class="pstat">
                <div class="pstat-val"><?php echo $swapCount; ?></div>
                <div class="pstat-lbl">Swaps</div>
            </div>
        </div>

        <a href="<?php echo BASE_PATH; ?>/my_books.php" class="btn btn-dark btn-full btn-sm">My Books</a>
        <a href="<?php echo BASE_PATH; ?>/add_book.php" class="btn btn-accent btn-full btn-sm" style="margin-top:8px;">+ Add Book</a>
    </div>

    <!-- ── Right: edit form ───────────────────────────────── -->
    <div class="profile-panel">
        <h2>Edit Profile</h2>

        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="" enctype="multipart/form-data">
            <?php echo csrfField(); ?>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="first_name">First Name <span class="req">*</span></label>
                    <input type="text" id="first_name" name="first_name" class="form-control"
                           required maxlength="50"
                           value="<?php echo e($user['first_name']); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="last_name">Last Name <span class="req">*</span></label>
                    <input type="text" id="last_name" name="last_name" class="form-control"
                           required maxlength="50"
                           value="<?php echo e($user['last_name']); ?>">
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="location">Location</label>
                <input type="text" id="location" name="location" class="form-control"
                       placeholder="e.g. Sydney, NSW" maxlength="100"
                       value="<?php echo e($user['location'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label class="form-label" for="bio">Bio</label>
                <textarea id="bio" name="bio" class="form-control"
                          placeholder="Tell other readers about yourself…"
                          maxlength="500"><?php echo e($user['bio'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="avatar">Profile Photo</label>
                <input type="file" id="avatar" name="avatar" class="form-control"
                       accept="image/jpeg,image/png,image/gif,image/webp">
                <p class="form-hint">JPG, PNG, GIF or WebP · Max 2 MB</p>
            </div>

            <button type="submit" class="btn btn-accent btn-lg">Save Changes</button>
        </form>
    </div>

</div>
</div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<?php
$pageTitle = 'Sign Up';
$pageCss   = 'auth';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
startSession();

if (isLoggedIn()) {
    header('Location: ' . BASE_PATH . '/index.php'); exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfCheck()) {
        $error = 'Invalid form submission.';
    } else {
        $username  = trim($_POST['username']   ?? '');
        $email     = trim($_POST['email']      ?? '');
        $firstName = trim($_POST['first_name'] ?? '');
        $lastName  = trim($_POST['last_name']  ?? '');
        $password  = $_POST['password']        ?? '';
        $confirm   = $_POST['confirm']         ?? '';
        $location  = trim($_POST['location']   ?? '');

        if ($password !== $confirm) {
            $error = 'Passwords do not match.';
        } elseif (strlen($password) < 8) {
            $error = 'Password must be at least 8 characters.';
        } else {
            $chk = $conn->prepare("SELECT user_id FROM users WHERE username = ? OR email = ? LIMIT 1");
            $chk->bind_param('ss', $username, $email);
            $chk->execute();
            if ($chk->get_result()->num_rows > 0) {
                $error = 'That username or email is already registered.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $ins  = $conn->prepare(
                    "INSERT INTO users (username, email, password_hash, first_name, last_name, location)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $ins->bind_param('ssssss', $username, $email, $hash, $firstName, $lastName, $location);
                $ins->execute();
                $newId = $ins->insert_id;
                $ins->close();

                session_regenerate_id(true);
                $_SESSION['user_id']  = $newId;
                $_SESSION['username'] = $username;
                $_SESSION['is_admin'] = 0;

                setFlash('success', 'Welcome to BookLoop, ' . $username . '!');
                header('Location: ' . BASE_PATH . '/index.php'); exit;
            }
            $chk->close();
        }
    }
}

$csrf = csrfToken();
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-icon">📚</div>
        <h1>Create Account</h1>
        <p class="auth-sub">Join BookLoop and start swapping books</p>

        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="first_name">First Name <span class="req">*</span></label>
                    <input type="text" id="first_name" name="first_name" class="form-control"
                           placeholder="Nirmal" required maxlength="50"
                           value="<?php echo e($_POST['first_name'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label" for="last_name">Last Name <span class="req">*</span></label>
                    <input type="text" id="last_name" name="last_name" class="form-control"
                           placeholder="Basnet" required maxlength="50"
                           value="<?php echo e($_POST['last_name'] ?? ''); ?>">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label" for="username">Username <span class="req">*</span></label>
                <input type="text" id="username" name="username" class="form-control"
                       placeholder="nirmal_hero" required minlength="3" maxlength="30"
                       pattern="[a-zA-Z0-9_]+" title="Letters, numbers and underscores only"
                       autocomplete="username" value="<?php echo e($_POST['username'] ?? ''); ?>">
                <p class="form-hint">Letters, numbers and underscores only</p>
            </div>
            <div class="form-group">
                <label class="form-label" for="email">Email <span class="req">*</span></label>
                <input type="email" id="email" name="email" class="form-control"
                       placeholder="you@example.com" required maxlength="100"
                       autocomplete="email" value="<?php echo e($_POST['email'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label class="form-label" for="location">Location</label>
                <input type="text" id="location" name="location" class="form-control"
                       placeholder="Sydney, NSW" maxlength="100"
                       value="<?php echo e($_POST['location'] ?? ''); ?>">
            </div>
            <hr class="auth-divider">
            <div class="form-group">
                <label class="form-label" for="password">Password <span class="req">*</span></label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="At least 8 characters" required minlength="8"
                       autocomplete="new-password">
            </div>
            <div class="form-group">
                <label class="form-label" for="confirm">Confirm Password <span class="req">*</span></label>
                <input type="password" id="confirm" name="confirm" class="form-control"
                       placeholder="Repeat your password" required minlength="8"
                       autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-accent btn-full btn-lg">Create Account</button>
        </form>

        <p class="auth-switch">
            Already have an account? <a href="<?php echo BASE_PATH; ?>/login.php">Log in</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

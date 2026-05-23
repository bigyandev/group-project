<?php
$pageTitle = 'Log In';
$pageCss   = 'auth';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';
startSession();

if (isLoggedIn()) {
    header('Location: ' . BASE_PATH . '/index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrfCheck()) {
        $error = 'Invalid form submission. Please try again.';
    } else {
        $identifier = trim($_POST['identifier'] ?? '');
        $password   = $_POST['password'] ?? '';

        $stmt = $conn->prepare(
            "SELECT user_id, username, password_hash, is_active, is_admin
             FROM users WHERE (username = ? OR email = ?) LIMIT 1"
        );
        $stmt->bind_param('ss', $identifier, $identifier);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$user) {
            $error = 'No account found with those details.';
        } elseif (!$user['is_active']) {
            $error = 'Your account has been suspended. Please contact support.';
        } elseif (!password_verify($password, $user['password_hash'])) {
            $error = 'Incorrect password. Please try again.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['is_admin'] = $user['is_admin'];

            $upd = $conn->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
            $upd->bind_param('i', $user['user_id']);
            $upd->execute();
            $upd->close();

            setFlash('success', 'Welcome back, ' . $user['username'] . '!');

            if ($user['is_admin']) {
                header('Location: ' . BASE_PATH . '/admin/index.php');
                exit;
            }
            header('Location: ' . BASE_PATH . '/index.php');
            exit;
        }
    }
}

$csrf = csrfToken();
require_once __DIR__ . '/includes/header.php';
?>

<div class="auth-page">
    <div class="auth-card">
        <div class="auth-icon">🔐</div>
        <h1>Welcome Back</h1>
        <p class="auth-sub">Log in to your BookLoop account</p>

        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo e($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">
            <div class="form-group">
                <label class="form-label" for="identifier">Username or Email <span class="req">*</span></label>
                <input type="text" id="identifier" name="identifier" class="form-control"
                       placeholder="your_username or email@example.com"
                       required maxlength="100" autocomplete="username">
            </div>
            <div class="form-group">
                <label class="form-label" for="password">Password <span class="req">*</span></label>
                <input type="password" id="password" name="password" class="form-control"
                       placeholder="Your password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-accent btn-full btn-lg">Log In</button>
        </form>

        <p class="auth-switch">
            Don't have an account? <a href="<?php echo BASE_PATH; ?>/register.php">Sign up free</a>
        </p>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login – BookLoop</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="login.css">
</head>
<body>

<?php
session_start();

$admin_user = "admin";
$admin_pass = "1234";
$error   = "";
$success = "";
$tab = isset($_GET['tab']) && $_GET['tab'] === 'signup' ? 'signup' : 'login';

if (isset($_POST['login'])) {
    if ($_POST['username'] === $admin_user && $_POST['password'] === $admin_pass) {
        $_SESSION['admin'] = $_POST['username'];
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid username or password.";
        $tab   = "login";
    }
}

if (isset($_POST['signup'])) {
    $success = "Account created! (Demo only — no database connected yet.)";
    $tab = "signup";
}
?>

<div class="login-wrapper">

  <!-- LEFT PANEL -->
  <div class="login-left">
    <a href="index.php" class="brand">Book<span>Loop</span></a>
    <h1>Your Next Great Read is <span>One Click Away</span></h1>
    <p>Join hundreds of readers sharing books in their community. Free to join, free to borrow, and always growing.</p>
    <div class="login-testimonial">
      <p>"BookLoop completely changed how I read. I have discovered more books in three months than in the past three years."</p>
      <cite>— Jordan M., member since 2024</cite>
    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="login-right">
    <div class="login-box">

      <!-- TAB TOGGLE (pure CSS — uses query param to switch) -->
      <div class="tab-toggle">
        <a href="login.php?tab=login"  class="<?php echo $tab === 'login'  ? 'tab-active' : ''; ?>">Log In</a>
        <a href="login.php?tab=signup" class="<?php echo $tab === 'signup' ? 'tab-active' : ''; ?>">Sign Up</a>
      </div>

      <?php if ($error !== ""): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>
      <?php if ($success !== ""): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <!-- LOGIN FORM -->
      <?php if ($tab === 'login'): ?>
        <h2>Welcome back</h2>
        <p class="sub">Sign in to access the full collection.</p>
        <form method="POST" action="login.php">
          <div class="form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Enter your username" required>
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
          </div>
          <button type="submit" name="login" class="form-submit">Log In</button>
        </form>
        <div class="demo-hint">
          Demo credentials: <strong>admin</strong> / <strong>1234</strong>
        </div>

      <!-- SIGNUP FORM -->
      <?php else: ?>
        <h2>Create account</h2>
        <p class="sub">Join the BookLoop community — it's free!</p>
        <form method="POST" action="login.php?tab=signup">
          <div class="form-group">
            <label for="full_name">Full Name</label>
            <input type="text" id="full_name" name="full_name" placeholder="Alex Rivera">
          </div>
          <div class="form-group">
            <label for="new_user">Username</label>
            <input type="text" id="new_user" name="new_user" placeholder="Choose a username" required>
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="you@example.com">
          </div>
          <div class="form-group">
            <label for="new_pass">Password</label>
            <input type="password" id="new_pass" name="new_pass" placeholder="Create a password" required>
          </div>
          <button type="submit" name="signup" class="form-submit">Create Account</button>
        </form>
      <?php endif; ?>

    </div>
  </div>

</div>

</body>
</html>

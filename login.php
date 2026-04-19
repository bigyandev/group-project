<?php
session_start();

$admin_user = "admin";
$admin_pass = "1234";

$error = "";
$success = "";

// LOGIN
if (isset($_POST['login'])) {
    if ($_POST['username'] == $admin_user && $_POST['password'] == $admin_pass) {
        $_SESSION['admin'] = $_POST['username'];
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid login details!";
    }
}

// SIGN UP (demo only — no DB yet)
if (isset($_POST['signup'])) {
    $success = "Account created! (Demo only)";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>BookLoop Auth</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<div class="container">
    <div class="card">

        <div class="toggle">
            <button onclick="showLogin()">Login</button>
            <button onclick="showSignup()">Sign Up</button>
        </div>

        <?php if ($error != "") { ?>
            <div class="error"><?php echo $error; ?></div>
        <?php } ?>

        <?php if ($success != "") { ?>
            <div class="success"><?php echo $success; ?></div>
        <?php } ?>

        <!-- LOGIN FORM -->
        <form method="POST" id="loginForm">
            <h2>Login</h2>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button name="login">Login</button>
        </form>

        <!-- SIGNUP FORM -->
        <form method="POST" id="signupForm" style="display:none;">
            <h2>Sign Up</h2>
            <input type="text" name="new_user" placeholder="Username" required>
            <input type="password" name="new_pass" placeholder="Password" required>
            <button name="signup">Create Account</button>
        </form>

    </div>
</div>


<script src="login.js"></script>
</body>
</html>
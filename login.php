<?php
session_start();

// Simple hardcoded admin credentials
$admin_user = "admin";
$admin_pass = "1234";

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    if ($username == $admin_user && $password == $admin_pass) {
        $_SESSION['admin'] = $username;
        header("Location: admin.php");
        exit();
    } else {
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - BookLoop</title>
</head>
<body>

<h2>Admin Login</h2>

<form method="POST" action="login.php">
    <input type="text" name="username" placeholder="Username" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit" name="login">Login</button>
</form>

</body>
</html>
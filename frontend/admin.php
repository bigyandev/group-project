<?php
session_start();

if (!isset($_SESSION['admin'])) {
    header("Location: admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel - BookLoop</title>
</head>
<body>

<h1>Welcome Admin </h1>
<p>You have successfully logged in.</p>

<a href="./backend/logout.php">Logout</a>

</body>
</html>
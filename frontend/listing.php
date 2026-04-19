<?php
session_start();

// Protect page ( only logged in admin )
if ( !isset( $_SESSION[ 'admin' ] ) ) {
    header( 'Location: login.php' );
    exit();
}
?>

<!DOCTYPE html>
<html>
     <head>
       <title>Book List - BookLoop</title>
       <link rel="stylesheet" href="listing.css">
       <link rel="stylesheet" href="style.css">
     </head>
     <body>
     <nav class="navbar">
      <a href="index.php" class="logo">BOOKLOOP</a>
      <ul>
         <li><a href="index.php">Home</a></li>
         <li><a href="#">About</a></li>
         <li><a href="listing.php">Listing</a></li>
         <li><a href="#">Contact</a></li>
         <li><a href="#">Request</a></li>
         <li><a href="login.php">LOG IN</a></li>

      </ul>
      </nav>

<div class = 'container'>
<div class = 'card'>
<h2>Available Books</h2>

<table border = '1' width = '100%' cellpadding = '10'>
<tr>
<th>Book Name</th>
<th>Author</th>
<th>Status</th>
</tr>

<tr>
<td>Harry Potter</td>
<td>J.K. Rowling</td>
<td>Available</td>
</tr>

<tr>
<td>The Alchemist</td>
<td>Paulo Coelho</td>
<td>Borrowed</td>
</tr>

<tr>
<td>Atomic Habits</td>
<td>James Clear</td>
<td>Available</td>
</tr>
</table>

<br>
<a href = 'admin.php'>⬅ Back to Dashboard</a>
</div>
</div>

</body>
</html>
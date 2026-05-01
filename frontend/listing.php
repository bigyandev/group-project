<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Book Listing – BookLoop</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="listing.css">
</head>
<body>

  <nav class="navbar">
    <a href="index.php" class="logo">Book<span>Loop</span></a>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="listing.php" class="active">Listing</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="request.php">Request</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="login.php" class="nav-btn">Log In</a></li>
    </ul>
  </nav>

  <div class="page-hero">
    <h1>Book <span>Listing</span></h1>
    <p>Browse and borrow from our community-curated collection.</p>
  </div>

  <!-- FILTERS (wire up action to your PHP handler) -->
  <form class="filters-bar" method="GET" action="listing.php">
    <input type="text" name="search" placeholder="Search by title or author..."
           value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
    <select name="status">
      <option value="">All Status</option>
      <option value="Available" <?php echo (isset($_GET['status']) && $_GET['status']==='Available') ? 'selected' : ''; ?>>Available</option>
      <option value="Borrowed"  <?php echo (isset($_GET['status']) && $_GET['status']==='Borrowed')  ? 'selected' : ''; ?>>Borrowed</option>
    </select>
    <select name="genre">
      <option value="">All Genres</option>
      <option value="Fantasy"  <?php echo (isset($_GET['genre']) && $_GET['genre']==='Fantasy')  ? 'selected' : ''; ?>>Fantasy</option>
      <option value="Fiction"  <?php echo (isset($_GET['genre']) && $_GET['genre']==='Fiction')  ? 'selected' : ''; ?>>Fiction</option>
      <option value="Self-Help"<?php echo (isset($_GET['genre']) && $_GET['genre']==='Self-Help') ? 'selected' : ''; ?>>Self-Help</option>
      <option value="Sci-Fi"   <?php echo (isset($_GET['genre']) && $_GET['genre']==='Sci-Fi')   ? 'selected' : ''; ?>>Sci-Fi</option>
      <option value="Classic"  <?php echo (isset($_GET['genre']) && $_GET['genre']==='Classic')  ? 'selected' : ''; ?>>Classic</option>
    </select>
    <button type="submit" class="btn btn-primary" style="padding: 9px 20px;">Search</button>
  </form>

  <div class="listing-top">
    <p>Showing all books</p>
    <a href="request.php" class="btn btn-primary" style="padding: 9px 20px; font-size: 0.84rem;">+ Request a Book</a>
  </div>

  <!-- BOOK GRID — replace static cards with PHP loop from your DB -->
  <div class="books-grid">

    <div class="book-card">
      <div class="book-cover c1">Harry Potter</div>
      <div class="book-info">
        <h4>Harry Potter</h4>
        <div class="author">J.K. Rowling</div>
        <div class="book-meta">
          <span class="badge badge-available">Available</span>
          <span class="genre-tag">Fantasy</span>
        </div>
        <form method="POST" action="borrow.php">
          <input type="hidden" name="book_id" value="1">
          <button type="submit" class="borrow-btn">Borrow Book</button>
        </form>
      </div>
    </div>

    <div class="book-card">
      <div class="book-cover c2">The Alchemist</div>
      <div class="book-info">
        <h4>The Alchemist</h4>
        <div class="author">Paulo Coelho</div>
        <div class="book-meta">
          <span class="badge badge-borrowed">Borrowed</span>
          <span class="genre-tag">Fiction</span>
        </div>
        <span class="borrow-btn-disabled">Currently Unavailable</span>
      </div>
    </div>

    <div class="book-card">
      <div class="book-cover c3">Atomic Habits</div>
      <div class="book-info">
        <h4>Atomic Habits</h4>
        <div class="author">James Clear</div>
        <div class="book-meta">
          <span class="badge badge-available">Available</span>
          <span class="genre-tag">Self-Help</span>
        </div>
        <form method="POST" action="borrow.php">
          <input type="hidden" name="book_id" value="3">
          <button type="submit" class="borrow-btn">Borrow Book</button>
        </form>
      </div>
    </div>

    <div class="book-card">
      <div class="book-cover c4">The Great Gatsby</div>
      <div class="book-info">
        <h4>The Great Gatsby</h4>
        <div class="author">F. Scott Fitzgerald</div>
        <div class="book-meta">
          <span class="badge badge-available">Available</span>
          <span class="genre-tag">Classic</span>
        </div>
        <form method="POST" action="borrow.php">
          <input type="hidden" name="book_id" value="4">
          <button type="submit" class="borrow-btn">Borrow Book</button>
        </form>
      </div>
    </div>

    <div class="book-card">
      <div class="book-cover c5">Dune</div>
      <div class="book-info">
        <h4>Dune</h4>
        <div class="author">Frank Herbert</div>
        <div class="book-meta">
          <span class="badge badge-available">Available</span>
          <span class="genre-tag">Sci-Fi</span>
        </div>
        <form method="POST" action="borrow.php">
          <input type="hidden" name="book_id" value="5">
          <button type="submit" class="borrow-btn">Borrow Book</button>
        </form>
      </div>
    </div>

    <div class="book-card">
      <div class="book-cover c6">1984</div>
      <div class="book-info">
        <h4>1984</h4>
        <div class="author">George Orwell</div>
        <div class="book-meta">
          <span class="badge badge-borrowed">Borrowed</span>
          <span class="genre-tag">Classic</span>
        </div>
        <span class="borrow-btn-disabled">Currently Unavailable</span>
      </div>
    </div>

    <div class="book-card">
      <div class="book-cover c7">Pride and Prejudice</div>
      <div class="book-info">
        <h4>Pride and Prejudice</h4>
        <div class="author">Jane Austen</div>
        <div class="book-meta">
          <span class="badge badge-available">Available</span>
          <span class="genre-tag">Classic</span>
        </div>
        <form method="POST" action="borrow.php">
          <input type="hidden" name="book_id" value="7">
          <button type="submit" class="borrow-btn">Borrow Book</button>
        </form>
      </div>
    </div>

    <div class="book-card">
      <div class="book-cover c8">The Midnight Library</div>
      <div class="book-info">
        <h4>The Midnight Library</h4>
        <div class="author">Matt Haig</div>
        <div class="book-meta">
          <span class="badge badge-available">Available</span>
          <span class="genre-tag">Fiction</span>
        </div>
        <form method="POST" action="borrow.php">
          <input type="hidden" name="book_id" value="8">
          <button type="submit" class="borrow-btn">Borrow Book</button>
        </form>
      </div>
    </div>

  </div>

  <footer>
    <span class="footer-logo">Book<span>Loop</span></span>
    <p>© 2025 BookLoop. Built for readers everywhere.</p>
    <p>
      <a href="about.php">About</a> &nbsp;·&nbsp;
      <a href="contact.php">Contact</a> &nbsp;·&nbsp;
      <a href="login.php">Login</a>
    </p>
  </footer>

</body>
</html>

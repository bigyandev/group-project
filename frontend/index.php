<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BookLoop – Share. Borrow. Read.</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="home.css">
</head>
<body>

  <nav class="navbar">
    <a href="index.php" class="logo">Book<span>Loop</span></a>
    <ul>
      <li><a href="index.php" class="active">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="listing.php">Listing</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="request.php">Request</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="login.php" class="nav-btn">Log In</a></li>
    </ul>
  </nav>

  <!-- HERO -->
  <div class="hero">
    <div class="hero-text">
      <h1>Every Book Deserves a <span>New Reader</span></h1>
      <p>BookLoop connects readers in your community. Borrow, lend, and discover books that keep on giving — because great stories should never gather dust.</p>
      <div class="hero-buttons">
        <a href="listing.php" class="btn btn-primary">Browse Books</a>
        <a href="about.php" class="btn btn-outline">Learn More</a>
      </div>
    </div>
    <div class="hero-visual">
      <div class="book-stack">
        <div class="book book-a"></div>
        <div class="book book-b"></div>
        <div class="book book-c"></div>
      </div>
    </div>
  </div>

  <!-- STATS -->
  <div class="stats-bar">
    <div class="stats-inner">
      <div>
        <div class="stat-num">240+</div>
        <div class="stat-label">Books Available</div>
      </div>
      <div>
        <div class="stat-num">180</div>
        <div class="stat-label">Active Members</div>
      </div>
      <div>
        <div class="stat-num">520</div>
        <div class="stat-label">Successful Borrows</div>
      </div>
      <div>
        <div class="stat-num">98%</div>
        <div class="stat-label">Happy Readers</div>
      </div>
    </div>
  </div>

  <!-- HOW IT WORKS -->
  <div class="how-section">
    <div class="how-inner">
      <h2 class="section-title">How It Works</h2>
      <p class="section-sub">Simple as turning a page.</p>
      <div class="how-grid">
        <div class="how-card">
          <span class="step">01</span>
          <h3>Create an Account</h3>
          <p>Sign up in seconds. No fees, no commitments — just a love for reading and sharing.</p>
        </div>
        <div class="how-card">
          <span class="step">02</span>
          <h3>Browse &amp; Request</h3>
          <p>Explore hundreds of books. Found one you want? Send a borrow request with one click.</p>
        </div>
        <div class="how-card">
          <span class="step">03</span>
          <h3>Read &amp; Return</h3>
          <p>Pick up your book, enjoy it, then return it so the loop continues for someone else.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- FEATURED BOOKS -->
  <div class="section">
    <h2 class="section-title">Featured Books</h2>
    <p class="section-sub">Available to borrow right now.</p>
    <div class="books-grid">
      <div class="book-card">
        <div class="book-cover cover-amber">Harry Potter</div>
        <div class="book-info">
          <h4>Harry Potter</h4>
          <div class="author">J.K. Rowling</div>
          <span class="badge badge-available">Available</span>
        </div>
      </div>
      <div class="book-card">
        <div class="book-cover cover-green">The Alchemist</div>
        <div class="book-info">
          <h4>The Alchemist</h4>
          <div class="author">Paulo Coelho</div>
          <span class="badge badge-borrowed">Borrowed</span>
        </div>
      </div>
      <div class="book-card">
        <div class="book-cover cover-dark">Atomic Habits</div>
        <div class="book-info">
          <h4>Atomic Habits</h4>
          <div class="author">James Clear</div>
          <span class="badge badge-available">Available</span>
        </div>
      </div>
      <div class="book-card">
        <div class="book-cover cover-red">The Great Gatsby</div>
        <div class="book-info">
          <h4>The Great Gatsby</h4>
          <div class="author">F. Scott Fitzgerald</div>
          <span class="badge badge-available">Available</span>
        </div>
      </div>
    </div>
    <div style="text-align: center;">
      <a href="listing.php" class="btn btn-outline">View All Books</a>
    </div>
  </div>

  <!-- QUOTE -->
  <div class="quote-section">
    <blockquote>"A reader lives a thousand lives before he dies. The man who never reads lives only one."</blockquote>
    <cite>— George R.R. Martin</cite>
  </div>

  <!-- CTA -->
  <div class="cta-band">
    <h2>Ready to Join the Loop?</h2>
    <p>Free, community-driven, and your next favourite book is waiting.</p>
    <a href="login.php" class="btn btn-dark">Get Started Today</a>
  </div>

  <footer>
    <span class="footer-logo">Book<span>Loop</span></span>
    <p>© 2025 BookLoop. Built for readers everywhere.</p>
    <p>
      <a href="about.php">About</a> &nbsp;·&nbsp;
      <a href="listing.php">Listing</a> &nbsp;·&nbsp;
      <a href="contact.php">Contact</a> &nbsp;·&nbsp;
      <a href="login.php">Login</a>
    </p>
  </footer>

</body>
</html>

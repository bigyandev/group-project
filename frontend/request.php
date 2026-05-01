<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Request a Book – BookLoop</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="request.css">
</head>
<body>

  <nav class="navbar">
    <a href="index.php" class="logo">Book<span>Loop</span></a>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="listing.php">Listing</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="request.php" class="active">Request</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="login.php" class="nav-btn">Log In</a></li>
    </ul>
  </nav>

  <div class="page-hero">
    <h1>Request a <span>Book</span></h1>
    <p>Can't find what you're looking for? Submit a request and our community will try to fulfil it.</p>
  </div>

  <div class="request-layout">

    <!-- LEFT FORM -->
    <div class="request-form-box">
      <h2>Submit a Request</h2>
      <p class="sub">Fill in as much detail as you can to help us find the right book.</p>

      <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>

      <form method="POST" action="request.php">
        <div class="form-group">
          <label for="title">Book Title *</label>
          <input type="text" id="title" name="title" placeholder="e.g. The Midnight Library" required>
        </div>
        <div class="form-group">
          <label for="author">Author Name</label>
          <input type="text" id="author" name="author" placeholder="e.g. Matt Haig">
        </div>
        <div class="form-group">
          <label for="genre">Genre</label>
          <select id="genre" name="genre">
            <option value="">Select a genre</option>
            <option>Fiction</option>
            <option>Non-Fiction</option>
            <option>Fantasy</option>
            <option>Sci-Fi</option>
            <option>Classic</option>
            <option>Self-Help</option>
            <option>Biography</option>
            <option>Mystery</option>
            <option>Other</option>
          </select>
        </div>
        <div class="form-group">
          <label for="isbn">ISBN (Optional)</label>
          <input type="text" id="isbn" name="isbn" placeholder="978-3-16-148410-0">
        </div>
        <div class="form-group">
          <label for="reason">Why do you want this book? (Optional)</label>
          <textarea id="reason" name="reason" placeholder="Share why you're requesting this book — it helps the community decide!"></textarea>
        </div>
        <div class="form-group">
          <label for="email">Your Email *</label>
          <input type="email" id="email" name="email" placeholder="you@example.com" required>
        </div>
        <button type="submit" class="form-submit">Submit Request</button>
      </form>
    </div>

    <!-- RIGHT SIDEBAR -->
    <div class="request-sidebar">
      <h2>Recent Requests</h2>

      <div class="request-card">
        <h4>The Midnight Library</h4>
        <div class="req-author">Matt Haig</div>
        <div class="req-meta">
          <span class="badge badge-pending">Pending</span>
          <span class="req-votes">12 votes</span>
        </div>
      </div>

      <div class="request-card">
        <h4>Sapiens</h4>
        <div class="req-author">Yuval Noah Harari</div>
        <div class="req-meta">
          <span class="badge badge-fulfilled">Fulfilled</span>
          <span class="req-votes">8 votes</span>
        </div>
      </div>

      <div class="request-card">
        <h4>Rich Dad Poor Dad</h4>
        <div class="req-author">Robert Kiyosaki</div>
        <div class="req-meta">
          <span class="badge badge-pending">Pending</span>
          <span class="req-votes">6 votes</span>
        </div>
      </div>

      <div class="request-card">
        <h4>The Power of Now</h4>
        <div class="req-author">Eckhart Tolle</div>
        <div class="req-meta">
          <span class="badge badge-pending">Pending</span>
          <span class="req-votes">4 votes</span>
        </div>
      </div>

      <div class="tips-box">
        <h3>Request Tips</h3>
        <ul>
          <li>Check the <a href="listing.php">book listing</a> first — your book might already be there.</li>
          <li>Include the ISBN for faster processing.</li>
          <li>The more votes a request gets, the sooner we will source it.</li>
          <li>Requests are usually fulfilled within 2–3 weeks.</li>
        </ul>
      </div>
    </div>

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

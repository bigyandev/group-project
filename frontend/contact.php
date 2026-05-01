<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact – BookLoop</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="contact.css">
</head>
<body>

  <nav class="navbar">
    <a href="index.php" class="logo">Book<span>Loop</span></a>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="listing.php">Listing</a></li>
      <li><a href="contact.php" class="active">Contact</a></li>
      <li><a href="request.php">Request</a></li>
      <li><a href="profile.php">Profile</a></li>
      <li><a href="login.php" class="nav-btn">Log In</a></li>
    </ul>
  </nav>

  <div class="page-hero">
    <h1>Contact <span>Us</span></h1>
    <p>Have a question or suggestion? We would love to hear from you.</p>
  </div>

  <div class="contact-layout">

    <!-- LEFT INFO -->
    <div class="contact-info">
      <h2>Get in Touch</h2>
      <p>Whether you need help with borrowing, want to donate books, or have ideas to make BookLoop better — our team is happy to help.</p>

      <div class="info-item">
        <div class="info-icon">&#9993;</div>
        <div>
          <h4>Email</h4>
          <p>hello@bookloop.com</p>
        </div>
      </div>

      <div class="info-item">
        <div class="info-icon">&#128205;</div>
        <div>
          <h4>Location</h4>
          <p>42 Reader's Lane<br>Sydney, NSW 2000</p>
        </div>
      </div>

      <div class="info-item">
        <div class="info-icon">&#128336;</div>
        <div>
          <h4>Opening Hours</h4>
          <p>Mon – Fri: 9am – 6pm<br>Saturday: 10am – 4pm</p>
        </div>
      </div>

      <div class="info-item">
        <div class="info-icon">&#128222;</div>
        <div>
          <h4>Phone</h4>
          <p>+61 2 9000 1234</p>
        </div>
      </div>

      <div class="contact-quote">
        <p>"Reading is a conversation. All books talk. But a good book listens as well."</p>
        <cite>— Mark Haddon</cite>
      </div>
    </div>

    <!-- RIGHT FORM -->
    <div class="contact-form-box">
      <h2>Send a Message</h2>
      <p class="sub">We typically respond within 24 hours.</p>

      <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
      <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
      <?php endif; ?>

      <form method="POST" action="contact.php">
        <div class="form-row">
          <div class="form-group">
            <label for="fname">First Name</label>
            <input type="text" id="fname" name="fname" placeholder="Alex" required>
          </div>
          <div class="form-group">
            <label for="lname">Last Name</label>
            <input type="text" id="lname" name="lname" placeholder="Rivera">
          </div>
        </div>
        <div class="form-group">
          <label for="email">Email Address</label>
          <input type="email" id="email" name="email" placeholder="you@example.com" required>
        </div>
        <div class="form-group">
          <label for="subject">Subject</label>
          <select id="subject" name="subject">
            <option>General Inquiry</option>
            <option>Borrowing Help</option>
            <option>Book Donation</option>
            <option>Technical Issue</option>
            <option>Feedback &amp; Suggestions</option>
          </select>
        </div>
        <div class="form-group">
          <label for="message">Message</label>
          <textarea id="message" name="message" placeholder="Tell us what's on your mind..." required></textarea>
        </div>
        <button type="submit" class="form-submit">Send Message</button>
      </form>
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

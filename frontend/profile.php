<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile – BookLoop</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="profile.css">
</head>
<body>

<?php
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'history';
$allowed = ['history', 'requests', 'settings'];
if (!in_array($tab, $allowed)) $tab = 'history';
?>

  <nav class="navbar">
    <a href="index.php" class="logo">Book<span>Loop</span></a>
    <ul>
      <li><a href="index.php">Home</a></li>
      <li><a href="about.php">About</a></li>
      <li><a href="listing.php">Listing</a></li>
      <li><a href="contact.php">Contact</a></li>
      <li><a href="request.php">Request</a></li>
      <li><a href="profile.php" class="active">Profile</a></li>
      <li><a href="login.php" class="nav-btn">Log In</a></li>
    </ul>
  </nav>

  <!-- PROFILE HERO -->
  <div class="profile-hero">
    <div class="profile-hero-inner">
      <div class="profile-avatar">&#128218;</div>
      <div class="profile-meta">
        <h1>Alex Rivera</h1>
        <div class="handle">@alexreads</div>
        <div class="joined">Member since January 2024 &nbsp;·&nbsp; Sydney, NSW</div>
      </div>
    </div>
  </div>

  <!-- TABS -->
  <div class="profile-tabs">
    <div class="tabs-inner">
      <a href="profile.php?tab=history"  class="tab-link <?php echo $tab==='history'  ? 'tab-active' : ''; ?>">Borrow History</a>
      <a href="profile.php?tab=requests" class="tab-link <?php echo $tab==='requests' ? 'tab-active' : ''; ?>">My Requests</a>
      <a href="profile.php?tab=settings" class="tab-link <?php echo $tab==='settings' ? 'tab-active' : ''; ?>">Settings</a>
    </div>
  </div>

  <!-- BODY -->
  <div class="profile-body">

    <!-- MAIN CONTENT -->
    <div>

      <!-- BORROW HISTORY -->
      <?php if ($tab === 'history'): ?>
      <div class="tab-pane active">
        <h2>Borrow History</h2>

        <div class="borrow-card">
          <div class="borrow-cover bc1"></div>
          <div class="borrow-info">
            <h4>Harry Potter &amp; the Sorcerer's Stone</h4>
            <div class="b-author">J.K. Rowling</div>
            <div class="borrow-dates">Borrowed: 01 Mar 2025 &rarr; Returned: 15 Mar 2025</div>
          </div>
          <span class="badge badge-available">Returned</span>
        </div>

        <div class="borrow-card">
          <div class="borrow-cover bc2"></div>
          <div class="borrow-info">
            <h4>Atomic Habits</h4>
            <div class="b-author">James Clear</div>
            <div class="borrow-dates">Borrowed: 10 Apr 2025 &rarr; Due: 24 Apr 2025</div>
          </div>
          <span class="badge badge-borrowed">Active</span>
        </div>

        <div class="borrow-card">
          <div class="borrow-cover bc3"></div>
          <div class="borrow-info">
            <h4>The Great Gatsby</h4>
            <div class="b-author">F. Scott Fitzgerald</div>
            <div class="borrow-dates">Borrowed: 14 Jan 2025 &rarr; Returned: 28 Jan 2025</div>
          </div>
          <span class="badge badge-available">Returned</span>
        </div>

        <div class="borrow-card">
          <div class="borrow-cover bc4"></div>
          <div class="borrow-info">
            <h4>Dune</h4>
            <div class="b-author">Frank Herbert</div>
            <div class="borrow-dates">Borrowed: 05 Dec 2024 &rarr; Returned: 02 Jan 2025</div>
          </div>
          <span class="badge badge-available">Returned</span>
        </div>
      </div>

      <!-- REQUESTS -->
      <?php elseif ($tab === 'requests'): ?>
      <div class="tab-pane active">
        <h2>My Requests</h2>
        <div class="borrow-card">
          <div class="borrow-cover" style="background:#5a4a8a;"></div>
          <div class="borrow-info">
            <h4>The Midnight Library</h4>
            <div class="b-author">Matt Haig</div>
            <div class="borrow-dates">Requested: 20 Apr 2025 &nbsp;·&nbsp; 12 votes</div>
          </div>
          <span class="badge badge-pending">Pending</span>
        </div>
        <p style="margin-top:20px; font-size:0.88rem; color:#7a6e60;">
          Want to request another book? <a href="request.php" style="color:#c8873a;">Submit a request</a>
        </p>
      </div>

      <!-- SETTINGS -->
      <?php elseif ($tab === 'settings'): ?>
      <div class="tab-pane active">
        <h2>Account Settings</h2>

        <div class="settings-section">
          <h3>Personal Information</h3>
          <form method="POST" action="profile.php?tab=settings">
            <div class="form-group">
              <label>Full Name</label>
              <input type="text" name="full_name" value="Alex Rivera">
            </div>
            <div class="form-group">
              <label>Username</label>
              <input type="text" name="username" value="alexreads">
            </div>
            <div class="form-group">
              <label>Email</label>
              <input type="email" name="email" value="alex@example.com">
            </div>
            <div class="form-group">
              <label>Location</label>
              <input type="text" name="location" value="Sydney, NSW">
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </form>
        </div>

        <div class="settings-section">
          <h3>Change Password</h3>
          <form method="POST" action="profile.php?tab=settings">
            <div class="form-group">
              <label>Current Password</label>
              <input type="password" name="current_pass" placeholder="••••••••">
            </div>
            <div class="form-group">
              <label>New Password</label>
              <input type="password" name="new_pass" placeholder="••••••••">
            </div>
            <div class="form-group">
              <label>Confirm New Password</label>
              <input type="password" name="confirm_pass" placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-primary">Update Password</button>
          </form>
        </div>
      </div>
      <?php endif; ?>

    </div>

    <!-- SIDEBAR -->
    <div>
      <div class="side-card">
        <h3>Reading Stats</h3>
        <div class="stat-row"><span class="s-key">Total Borrowed</span><span class="s-val">14</span></div>
        <div class="stat-row"><span class="s-key">Currently Reading</span><span class="s-val">1</span></div>
        <div class="stat-row"><span class="s-key">Returned On Time</span><span class="s-val">13 / 13</span></div>
        <div class="stat-row"><span class="s-key">Days as Member</span><span class="s-val">485</span></div>
      </div>

      <div class="side-card">
        <h3>Favourite Genres</h3>
        <div class="genre-pills">
          <span class="genre-pill top">Fantasy</span>
          <span class="genre-pill top">Sci-Fi</span>
          <span class="genre-pill">Classic</span>
          <span class="genre-pill">Self-Help</span>
          <span class="genre-pill">Fiction</span>
        </div>
      </div>

      <div class="side-card">
        <h3>Achievements</h3>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px;">
          <div style="text-align:center; padding:14px; background:#f0ece4; border-radius:9px;">
            <div style="font-size:1.5rem;">&#128218;</div>
            <div style="font-size:0.70rem; color:#7a6e60; margin-top:5px; font-weight:bold;">Bookworm</div>
          </div>
          <div style="text-align:center; padding:14px; background:#f0ece4; border-radius:9px;">
            <div style="font-size:1.5rem;">&#11088;</div>
            <div style="font-size:0.70rem; color:#7a6e60; margin-top:5px; font-weight:bold;">Top Reviewer</div>
          </div>
          <div style="text-align:center; padding:14px; background:#f0ece4; border-radius:9px;">
            <div style="font-size:1.5rem;">&#128260;</div>
            <div style="font-size:0.70rem; color:#7a6e60; margin-top:5px; font-weight:bold;">Loop King</div>
          </div>
          <div style="text-align:center; padding:14px; background:#e8e0d0; border-radius:9px; opacity:0.5;">
            <div style="font-size:1.5rem;">&#127942;</div>
            <div style="font-size:0.70rem; color:#7a6e60; margin-top:5px; font-weight:bold;">Locked</div>
          </div>
        </div>
      </div>
    </div>

  </div><!-- end profile-body -->

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

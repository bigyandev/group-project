<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard – BookLoop</title>
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="admin.css">
</head>
<body>

<?php
session_start();
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit();
}
?>

  <nav class="navbar">
    <a href="index.php" class="logo">Book<span>Loop</span></a>
    <ul>
      <li><a href="index.php">View Site</a></li>
      <li><a href="listing.php">Listing</a></li>
      <li><a href="admin.php" class="active">Dashboard</a></li>
      <li><a href="backend/logout.php" class="nav-btn" style="background:#a03520;">Log Out</a></li>
    </ul>
  </nav>

  <!-- SIDEBAR -->
  <aside class="admin-sidebar">
    <span class="sidebar-label">Overview</span>
    <a href="admin.php" class="sidebar-link active">Dashboard</a>
    <a href="listing.php" class="sidebar-link">Books</a>

    <span class="sidebar-label">Manage</span>
    <a href="#" class="sidebar-link">Members</a>
    <a href="#" class="sidebar-link">Borrows</a>
    <a href="request.php" class="sidebar-link">Requests</a>

    <span class="sidebar-label">Other</span>
    <a href="contact.php" class="sidebar-link">Messages</a>
    <a href="#" class="sidebar-link">Settings</a>
    <a href="backend/logout.php" class="sidebar-link logout">Log Out</a>
  </aside>

  <!-- MAIN -->
  <main class="admin-main">

    <div class="admin-header">
      <h1>Welcome, <?php echo htmlspecialchars($_SESSION['admin']); ?> &#128075;</h1>
      <p>Here is what is happening in the BookLoop community today.</p>
    </div>

    <!-- STAT CARDS -->
    <div class="stat-grid">
      <div class="stat-card">
        <div class="s-label">Total Books</div>
        <div class="s-num">248</div>
        <div class="s-change s-up">+12 this week</div>
      </div>
      <div class="stat-card">
        <div class="s-label">Members</div>
        <div class="s-num">183</div>
        <div class="s-change s-up">+7 new</div>
      </div>
      <div class="stat-card">
        <div class="s-label">Active Borrows</div>
        <div class="s-num">41</div>
        <div class="s-change s-down">3 overdue</div>
      </div>
      <div class="stat-card">
        <div class="s-label">Open Requests</div>
        <div class="s-num">14</div>
        <div class="s-change" style="color:#7a6e60;">2 fulfilled</div>
      </div>
    </div>

    <!-- BOOKS TABLE + ACTIVITY -->
    <div class="two-col">

      <div class="panel">
        <div class="panel-header">
          <h2>Book Inventory</h2>
          <a href="listing.php">View all</a>
        </div>
        <table class="admin-table">
          <thead>
            <tr>
              <th>Title</th>
              <th>Author</th>
              <th>Genre</th>
              <th>Status</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><strong>Harry Potter</strong></td>
              <td>J.K. Rowling</td>
              <td>Fantasy</td>
              <td><span class="badge badge-available">Available</span></td>
              <td><button class="tbl-btn">Edit</button></td>
            </tr>
            <tr>
              <td><strong>The Alchemist</strong></td>
              <td>Paulo Coelho</td>
              <td>Fiction</td>
              <td><span class="badge badge-borrowed">Borrowed</span></td>
              <td><button class="tbl-btn">Edit</button></td>
            </tr>
            <tr>
              <td><strong>Atomic Habits</strong></td>
              <td>James Clear</td>
              <td>Self-Help</td>
              <td><span class="badge badge-available">Available</span></td>
              <td><button class="tbl-btn">Edit</button></td>
            </tr>
            <tr>
              <td><strong>Dune</strong></td>
              <td>Frank Herbert</td>
              <td>Sci-Fi</td>
              <td><span class="badge badge-available">Available</span></td>
              <td><button class="tbl-btn">Edit</button></td>
            </tr>
            <tr>
              <td><strong>1984</strong></td>
              <td>George Orwell</td>
              <td>Classic</td>
              <td><span class="badge badge-borrowed">Borrowed</span></td>
              <td><button class="tbl-btn">Edit</button></td>
            </tr>
          </tbody>
        </table>
      </div>

      <div class="panel">
        <div class="panel-header">
          <h2>Recent Activity</h2>
          <a href="#">See all</a>
        </div>
        <div class="activity-item">
          <div class="act-dot dot-borrow">&#128228;</div>
          <div>
            <div class="act-name">Jordan M. borrowed</div>
            <div class="act-desc">The Great Gatsby</div>
            <div class="act-time">2 hours ago</div>
          </div>
        </div>
        <div class="activity-item">
          <div class="act-dot dot-return">&#128229;</div>
          <div>
            <div class="act-name">Sam K. returned</div>
            <div class="act-desc">Atomic Habits</div>
            <div class="act-time">5 hours ago</div>
          </div>
        </div>
        <div class="activity-item">
          <div class="act-dot dot-request">&#128221;</div>
          <div>
            <div class="act-name">New request</div>
            <div class="act-desc">The Midnight Library</div>
            <div class="act-time">Yesterday</div>
          </div>
        </div>
        <div class="activity-item">
          <div class="act-dot dot-borrow">&#128228;</div>
          <div>
            <div class="act-name">Mia L. borrowed</div>
            <div class="act-desc">1984</div>
            <div class="act-time">Yesterday</div>
          </div>
        </div>
        <div class="activity-item">
          <div class="act-dot dot-return">&#128229;</div>
          <div>
            <div class="act-name">Alex R. returned</div>
            <div class="act-desc">Harry Potter</div>
            <div class="act-time">2 days ago</div>
          </div>
        </div>
      </div>

    </div>

    <!-- PENDING REQUESTS TABLE -->
    <div class="panel">
      <div class="panel-header">
        <h2>Pending Requests</h2>
        <a href="request.php">View all</a>
      </div>
      <table class="admin-table">
        <thead>
          <tr>
            <th>Book Title</th>
            <th>Requested By</th>
            <th>Date</th>
            <th>Votes</th>
            <th>Status</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td><strong>The Midnight Library</strong></td>
            <td>Sarah K.</td>
            <td>28 Apr 2025</td>
            <td>12</td>
            <td><span class="badge badge-pending">Pending</span></td>
            <td><button class="tbl-btn">Fulfil</button></td>
          </tr>
          <tr>
            <td><strong>Rich Dad Poor Dad</strong></td>
            <td>Tom B.</td>
            <td>25 Apr 2025</td>
            <td>6</td>
            <td><span class="badge badge-pending">Pending</span></td>
            <td><button class="tbl-btn">Fulfil</button></td>
          </tr>
          <tr>
            <td><strong>The Power of Now</strong></td>
            <td>Priya M.</td>
            <td>22 Apr 2025</td>
            <td>4</td>
            <td><span class="badge badge-pending">Pending</span></td>
            <td><button class="tbl-btn">Fulfil</button></td>
          </tr>
        </tbody>
      </table>
    </div>

  </main>

</body>
</html>

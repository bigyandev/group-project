<?php
// header.php  –  included at the top of every page
// Before including this file, each page sets:
//   $pageTitle (string)  – shown in <title>
//   $pageCss   (string)  – name of the page-specific CSS file (without .css)

$currentPage  = basename($_SERVER['PHP_SELF']);
$unread       = isLoggedIn() ? unreadCount($conn) : 0;
$flash        = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($pageTitle ?? 'BookLoop'); ?> — <?php echo SITE_NAME; ?></title>

    <!-- Global styles (nav, footer, buttons, forms, badges) -->
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/global.css">

    <!-- Page-specific styles -->
    <?php if (!empty($pageCss)): ?>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/<?php echo e($pageCss); ?>.css">
    <?php endif; ?>
</head>
<body>

<!-- ── Top Navigation ─────────────────────────────────── -->
<header class="site-header">
    <div class="container nav-inner">

        <!-- Logo -->
        <a href="<?php echo BASE_PATH; ?>/index.php" class="logo">
            <span class="logo-icon">📚</span>
            <span class="logo-text"><?php echo SITE_NAME; ?></span>
        </a>

        <!-- Centre links -->
        <nav class="main-nav" id="mainNav">
            <a href="<?php echo BASE_PATH; ?>/index.php"
               class="nav-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>">Home</a>
            <a href="<?php echo BASE_PATH; ?>/catalog.php"
               class="nav-link <?php echo $currentPage === 'catalog.php' ? 'active' : ''; ?>">Browse Books</a>

            <?php if (isLoggedIn()): ?>
            <a href="<?php echo BASE_PATH; ?>/my_books.php"
               class="nav-link <?php echo $currentPage === 'my_books.php' ? 'active' : ''; ?>">My Books</a>
            <a href="<?php echo BASE_PATH; ?>/requests.php"
               class="nav-link <?php echo $currentPage === 'requests.php' ? 'active' : ''; ?>">Requests</a>
            <?php endif; ?>

            <?php if (isAdmin()): ?>
            <a href="<?php echo BASE_PATH; ?>/admin/index.php"
               class="nav-link nav-admin <?php echo $currentPage === 'index.php' && strpos($_SERVER['PHP_SELF'], 'admin') !== false ? 'active' : ''; ?>">
                ⚙ Admin
            </a>
            <?php endif; ?>
        </nav>

        <!-- Right side -->
        <div class="nav-right">
            <?php if (isLoggedIn()): ?>

            <!-- Notification bell -->
            <a href="<?php echo BASE_PATH; ?>/notifications.php" class="nav-bell" aria-label="Notifications">
                🔔
                <?php if ($unread > 0): ?>
                <span class="bell-count"><?php echo $unread; ?></span>
                <?php endif; ?>
            </a>

            <!-- User dropdown -->
            <div class="nav-user" id="navUser">
                <button class="user-toggle" id="userToggle" type="button">
                    <?php echo e($_SESSION['username']); ?> ▾
                </button>
                <div class="user-menu" id="userMenu">
                    <a href="<?php echo BASE_PATH; ?>/profile.php">My Profile</a>
                    <a href="<?php echo BASE_PATH; ?>/add_book.php">+ Add Book</a>
                    <hr>
                    <a href="<?php echo BASE_PATH; ?>/logout.php" class="logout-link">Log Out</a>
                </div>
            </div>

            <?php else: ?>
            <a href="<?php echo BASE_PATH; ?>/login.php"    class="btn btn-ghost">Log In</a>
            <a href="<?php echo BASE_PATH; ?>/register.php" class="btn btn-accent">Sign Up</a>
            <?php endif; ?>

            <!-- Mobile hamburger -->
            <button class="hamburger" id="hamburger" type="button" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>

    </div>
</header>

<!-- ── Flash message ──────────────────────────────────── -->
<?php if ($flash): ?>
<div class="flash flash-<?php echo e($flash['type']); ?>">
    <div class="container"><?php echo e($flash['message']); ?></div>
</div>
<?php endif; ?>

<!-- ── Page content starts here ───────────────────────── -->
<main class="site-main">

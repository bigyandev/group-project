<?php // footer.php  –  closes <main> and renders the footer ?>
</main><!-- /site-main -->

<footer class="site-footer">
    <div class="container footer-inner">

        <div class="footer-brand">
            <a href="<?php echo BASE_PATH; ?>/index.php" class="logo">
                <span class="logo-icon">📚</span>
                <span class="logo-text"><?php echo SITE_NAME; ?></span>
            </a>
            <p>A community platform for swapping books with people near you.</p>
        </div>

        <div class="footer-col">
            <h4>Explore</h4>
            <a href="<?php echo BASE_PATH; ?>/catalog.php">Browse Books</a>
            <a href="<?php echo BASE_PATH; ?>/register.php">Join Free</a>
        </div>

        <div class="footer-col">
            <h4>Account</h4>
            <?php if (isLoggedIn()): ?>
            <a href="<?php echo BASE_PATH; ?>/add_book.php">Add a Book</a>
            <a href="<?php echo BASE_PATH; ?>/my_books.php">My Books</a>
            <a href="<?php echo BASE_PATH; ?>/requests.php">Requests</a>
            <?php else: ?>
            <a href="<?php echo BASE_PATH; ?>/login.php">Log In</a>
            <a href="<?php echo BASE_PATH; ?>/register.php">Register</a>
            <?php endif; ?>
        </div>

    </div>
    <div class="footer-bottom">
        <p>© <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> — ICT312 Advanced Web Information Systems</p>
    </div>
</footer>

<!-- Main JS: slideshow, mobile nav, dropdown -->
<script src="<?php echo BASE_PATH; ?>/js/main.js"></script>
</body>
</html>

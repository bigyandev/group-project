// ============================================================
//  main.js  –  BookLoop front-end scripts
//  1. Hero slideshow (auto-advance, arrows, dots, keyboard, swipe)
//  2. Mobile hamburger nav
//  3. User dropdown menu
// ============================================================


// ── 1. Hero Slideshow ────────────────────────────────────────

(function () {
    var slides   = document.querySelectorAll('.slide');
    var dots     = document.querySelectorAll('.dot');
    var prevBtn  = document.getElementById('slidePrev');
    var nextBtn  = document.getElementById('slideNext');

    // Only run if slideshow elements exist on this page
    if (slides.length === 0) return;

    var current  = 0;
    var timer    = null;
    var INTERVAL = 5000;   // milliseconds between auto-advance

    // Show the slide at position n
    function goTo(n) {
        slides[current].classList.remove('active');
        dots[current].classList.remove('active');

        current = (n + slides.length) % slides.length;

        slides[current].classList.add('active');
        dots[current].classList.add('active');
    }

    function next() { goTo(current + 1); }
    function prev() { goTo(current - 1); }

    // Start auto-timer
    function startTimer() {
        timer = setInterval(next, INTERVAL);
    }

    // Restart timer after manual interaction
    function resetTimer() {
        clearInterval(timer);
        startTimer();
    }

    // Arrow buttons
    if (nextBtn) {
        nextBtn.addEventListener('click', function () { next(); resetTimer(); });
    }
    if (prevBtn) {
        prevBtn.addEventListener('click', function () { prev(); resetTimer(); });
    }

    // Dot buttons
    dots.forEach(function (dot, i) {
        dot.addEventListener('click', function () { goTo(i); resetTimer(); });
    });

    // Keyboard arrows
    document.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight') { next(); resetTimer(); }
        if (e.key === 'ArrowLeft')  { prev(); resetTimer(); }
    });

    // Touch / swipe support
    var touchStartX = 0;
    var slideshow   = document.querySelector('.slideshow');

    if (slideshow) {
        slideshow.addEventListener('touchstart', function (e) {
            touchStartX = e.changedTouches[0].clientX;
        }, { passive: true });

        slideshow.addEventListener('touchend', function (e) {
            var dx = e.changedTouches[0].clientX - touchStartX;
            if (Math.abs(dx) > 50) {
                if (dx < 0) { next(); } else { prev(); }
                resetTimer();
            }
        }, { passive: true });
    }

    // Kick off
    startTimer();
}());


// ── 2. Mobile hamburger nav ──────────────────────────────────

(function () {
    var hamburger = document.getElementById('hamburger');
    var mainNav   = document.getElementById('mainNav');

    if (!hamburger || !mainNav) return;

    hamburger.addEventListener('click', function () {
        mainNav.classList.toggle('open');
    });

    // Close nav when a link inside it is clicked
    mainNav.addEventListener('click', function (e) {
        if (e.target.tagName === 'A') {
            mainNav.classList.remove('open');
        }
    });
}());


// ── 3. User dropdown ─────────────────────────────────────────

(function () {
    var toggle   = document.getElementById('userToggle');
    var menu     = document.getElementById('userMenu');

    if (!toggle || !menu) return;

    // Toggle menu open/closed
    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        menu.classList.toggle('open');
    });

    // Close menu when clicking anywhere outside it
    document.addEventListener('click', function () {
        menu.classList.remove('open');
    });
}());

<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header>
    <nav class="navbar">
        <div class="logo">HotelReservation</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="#hotels">Hotels</a></li>

            <?php if (empty($_SESSION['user_id'])): ?>
                <!-- Open modals instead of redirecting -->
                <li><a href="#" class="btn" id="openLogin">Login</a></li>
                <li><a href="#" class="btn" id="openRegister">Register</a></li>
            <?php else: ?>
                <li><a href="pages/search.php" class="btn">Dashboard</a></li>
                <li><a href="pages/logout.php" class="btn">Logout</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
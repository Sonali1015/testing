<?php
// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hotel Reservation System | Home</title>
    <meta name="description"
        content="Book hotels easily with our Hotel Reservation System. Search, book, and manage your reservations all in one place.">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

    <?php include 'includes/header.php'; ?>

    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-text">
            <h1>Find and Book Hotels Easily</h1>
            <p>Search, book, and manage your hotel reservations in one place.</p>
            <a href="pages/search.php" class="btn hero-btn">Search Hotels</a>
        </div>
    </section>

    <!-- Hotels Section -->
    <section id="hotels" class="hotels-section">
        <h2>Popular Hotels</h2>
        <div class="hotel-cards">
            <!-- Static hotel cards (can be replaced with DB loop later) -->
            <div class="hotel-card">
                <img src="assets/images/luxury_stay.jpg" alt="Luxury Stay">
                <h3>Luxury Stay</h3>
                <p>Top-rated hotel with premium services</p>
            </div>
            <div class="hotel-card">
                <img src="assets/images/ocean_view.jpg" alt="Ocean View">
                <h3>Ocean View</h3>
                <p>Relax with stunning sea views</p>
            </div>
            <div class="hotel-card">
                <img src="assets/images/hilltop_resort.jpeg" alt="Hilltop Resort">
                <h3>Hilltop Resort</h3>
                <p>Peaceful retreat in the mountains</p>
            </div>
            <div class="hotel-card">
                <img src="assets/images/golden_sands.jpg" alt="Golden Sands">
                <h3>Golden Sands</h3>
                <p>Beachside luxury resort</p>
            </div>
            <div class="hotel-card">
                <img src="assets/images/city_lights.webp" alt="City Lights">
                <h3>City Lights</h3>
                <p>Modern stay in the heart of the city</p>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <!-- Login Modal -->
    <div id="loginModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeLogin">&times;</span>
            <h2>Login</h2>
            <form method="POST" action="pages/login.php" id="loginFormModal">
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Login</button>
            </form>
        </div>
    </div>

    <!-- Register Modal -->
    <div id="registerModal" class="modal">
        <div class="modal-content">
            <span class="close" id="closeRegister">&times;</span>
            <h2>Register</h2>
            <form method="POST" action="pages/register.php" id="registerFormModal">
                <input type="text" name="name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <button type="submit">Register</button>
            </form>
        </div>
    </div>

    <script>
        // Get modal elements
        const loginModal = document.getElementById("loginModal");
        const registerModal = document.getElementById("registerModal");

        // Get buttons that open modals
        const openLogin = document.getElementById("openLogin");
        const openRegister = document.getElementById("openRegister");

        // Get <span> elements that close modals
        const closeLogin = document.getElementById("closeLogin");
        const closeRegister = document.getElementById("closeRegister");

        // Open Login Modal
        if (openLogin) {
            openLogin.onclick = function(e) {
                e.preventDefault();
                loginModal.style.display = "flex";
            }
        }

        // Open Register Modal
        if (openRegister) {
            openRegister.onclick = function(e) {
                e.preventDefault();
                registerModal.style.display = "flex";
            }
        }

        // Close Login Modal
        if (closeLogin) {
            closeLogin.onclick = function() {
                loginModal.style.display = "none";
            }
        }

        // Close Register Modal
        if (closeRegister) {
            closeRegister.onclick = function() {
                registerModal.style.display = "none";
            }
        }

        // Close modal if click outside content
        window.onclick = function(event) {
            if (event.target == loginModal) loginModal.style.display = "none";
            if (event.target == registerModal) registerModal.style.display = "none";
        }
    </script>
</body>

</html>
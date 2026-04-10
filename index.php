<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

// 2. Stats Fetch Karna
$total_books = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM books"))['total'];
$total_members = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM members"))['total'];
$total_issued = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM transactions WHERE status='Issued'"))['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vidyashala - Digital Library Dashboard</title>
    
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

    <nav class="navbar">
        <div class="logo">Vidyashala</div>
        <ul class="nav-links">
            <li><a href="index.php" class="active">Home</a></li>
            <li><a href="books.php">Books</a></li>
            <li><a href="members.php">Members</a></li>
            <li><a href="transactions.php">Issue/Return</a></li>
            <li><a href="login.php" class="login-btn">Admin Login</a></li>
        </ul>
    </nav>

    <header class="hero">
        <div class="hero-overlay">
            <div class="hero-content">
                <h1>Welcome to Digital Library</h1>
                <p>Kitabon ki duniya ab aapke fingertips par. Manage, track, and discover effortlessly.</p>
                <div class="search-box">
                    <form action="books.php" method="GET" style="display: flex; width: 100%;">
                        <input type="text" name="search" placeholder="Search by title, author or ISBN...">
                        <button type="submit">Search</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <section class="stats">
        <div class="stat-card">
            <i class="fas fa-book-open stat-icon"></i>
            <h3><?php echo number_format($total_books); ?></h3>
            <p>Total Books</p>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-users stat-icon"></i>
            <h3><?php echo number_format($total_members); ?></h3>
            <p>Active Members</p>
        </div>
        
        <div class="stat-card">
            <i class="fas fa-exchange-alt stat-icon" style="color: #e67e22;"></i>
            <h3><?php echo number_format($total_issued); ?></h3>
            <p>Issued Right Now</p>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-container">
            
            <div class="footer-box">
                <h3>Our Address</h3>
                <p><i class="fas fa-map-marker-alt"></i> 45,C Panchayat Govindpur, <br> Bokaro Thermal, Bokaro India</p>
                <p><i class="fas fa-phone"></i> +91 6200858397</p>
                <p><i class="fas fa-envelope"></i> contact@pustakmag.com</p>
            </div>

            <div class="footer-box">
                <h3>Support</h3>
                <ul>
                    <li><a href="#">Help Center</a></li>
                    <li><a href="#">Give Feedback</a></li>
                    <li><a href="#">FAQs</a></li>
                    <li><a href="#">Contact Us</a></li>
                </ul>
            </div>

            <div class="footer-box">
                <h3>Follow Us</h3>
                <div class="social-icons">
                    <a href="#" class="fb-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="ins-icon"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="tw-icon"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="ln-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>

        </div>
        <div class="footer-bottom">
            &copy; 2026 Vidyashala Library Management System | Created with ❤️ in Bokaro
        </div>
    </footer>

</body>
</html>
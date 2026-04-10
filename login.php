<?php
session_start();
include 'config.php'; // Database connection include karein

$error = "";

if (isset($_POST['login_submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['admin_email']);
    $password = mysqli_real_escape_string($conn, $_POST['admin_pass']);

    // Database mein check karein
    $query = "SELECT * FROM admin WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        // Session set karein
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_email'] = $email;
        
        // Dashboard (index.php) par redirect karein
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid Email or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Vidyashala</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body class="login-body">

    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo"><i class="fas fa-university"></i></div>
                <h2>Admin Login</h2>
                <p>Welcome back! Please enter your details.</p>
                
                <?php if($error != "") { ?>
                    <p style="color: #ff4d4d; background: rgba(255, 77, 77, 0.1); padding: 10px; border-radius: 5px; font-size: 0.8rem; margin-top: 10px;">
                        <?php echo $error; ?>
                    </p>
                <?php } ?>
            </div>

            <form action="login.php" method="POST" class="login-form">
                <div class="input-field">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="admin_email" placeholder="Admin Email" required>
                </div>
                
                <div class="input-field">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="admin_pass" placeholder="Password" required>
                </div>

                <div class="login-options">
                    <label><input type="checkbox"> Remember me</label>
                    <a href="#">Forgot Password?</a>
                </div>

                <button type="submit" name="login_submit" class="login-submit-btn">Login to Dashboard</button>
            </form>

            <div class="login-footer">
                <p>Not an Admin? <a href="index.php">Go to Home</a></p>
            </div>
        </div>
    </div>

</body>
</html>
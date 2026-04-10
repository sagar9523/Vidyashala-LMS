<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';
// 1. Database Connection include karein


// 2. Check karein ki URL mein 'id' pass hui hai ya nahi
if (isset($_GET['id']) && !empty($_GET['id'])) {
    
    // ID ko secure banana (SQL Injection se bachne ke liye)
    $id = mysqli_real_escape_string($conn, $_GET['id']);

    // 3. Delete Query taiyar karna
    $sql = "DELETE FROM books WHERE id = '$id'";

    // 4. Query ko run karna
    if (mysqli_query($conn, $sql)) {
        // Agar delete ho gaya toh success message ke saath wapas bhej dena
        echo "<script>
                alert('Book successfully delete ho gayi!');
                window.location.href = 'books.php';
              </script>";
    } else {
        // Agar koi error aaye
        echo "Error: " . mysqli_error($conn);
    }
} else {
    // Agar bina ID ke koi is file ko access kare toh wapas bhej dein
    header("Location: books.php");
    exit();
}

// Connection close karna
mysqli_close($conn);
?>
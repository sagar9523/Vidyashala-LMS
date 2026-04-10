<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

if (isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    
    // Delete query
    $sql = "DELETE FROM members WHERE id = '$id'";

    if (mysqli_query($conn, $sql)) {
        // Success hone par wapas bhej dena
        echo "<script>
                alert('Member successfully delete ho gaya!');
                window.location.href = 'members.php';
              </script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    header("Location: members.php");
}
?>
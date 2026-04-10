<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'config.php'; 

// 1. BOOK ISSUE LOGIC
if (isset($_POST['confirm_issue'])) {
    $m_id = mysqli_real_escape_string($conn, $_POST['m_id']);
    $b_id = mysqli_real_escape_string($conn, $_POST['b_id']);
    $i_date = $_POST['issue_date'];
    $d_date = $_POST['due_date'];

    // Pehle check karein ki book available hai ya nahi
    $check = mysqli_query($conn, "SELECT status FROM books WHERE book_id_code = '$b_id'");
    $book_data = mysqli_fetch_assoc($check);

    if ($book_data['status'] == 'Available') {
        // Transaction record insert karein
        $sql = "INSERT INTO transactions (book_id_code, member_id_code, issue_date, due_date) 
                VALUES ('$b_id', '$m_id', '$i_date', '$d_date')";
        
        if (mysqli_query($conn, $sql)) {
            // Books table mein status 'Issued' karein
            mysqli_query($conn, "UPDATE books SET status = 'Issued' WHERE book_id_code = '$b_id'");
            header("Location: transactions.php?msg=issued");
        }
    } else {
        echo "<script>alert('Error: Ye book pehle se Issued hai!');</script>";
    }
}

// 2. BOOK RETURN LOGIC
if (isset($_GET['return_id']) && isset($_GET['b_id'])) {
    $t_id = $_GET['return_id'];
    $b_id = $_GET['b_id'];
    $r_date = date('Y-m-d');

    // Transaction update karein
    mysqli_query($conn, "UPDATE transactions SET return_date = '$r_date', status = 'Returned' WHERE id = '$t_id'");
    // Book ko wapas 'Available' karein
    mysqli_query($conn, "UPDATE books SET status = 'Available' WHERE book_id_code = '$b_id'");
    
    header("Location: transactions.php?msg=returned");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue/Return - Vidyashala</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

    <nav class="navbar">
        <div class="logo">Vidyashala</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="books.php">Books</a></li>
            <li><a href="members.php">Members</a></li>
            <li><a href="transactions.php" class="active">Issue/Return</a></li>
            <li><a href="login.php" class="login-btn">Admin Login</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2><i class="fas fa-exchange-alt"></i> Book Transactions</h2>
            <button class="add-btn" onclick="openIssueModal()"><i class="fas fa-plus"></i> Issue New Book</button>
        </div>

        <div class="table-wrapper">
            <table class="books-table">
                <thead>
                    <tr>
                        <th>Book ID</th>
                        <th>Member ID</th>
                        <th>Issue Date</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Sirf wahi dikhayenge jo abhi tak Return nahi hui hain
                    $res = mysqli_query($conn, "SELECT * FROM transactions WHERE status = 'Issued' ORDER BY id DESC");
                    
                    if(mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_assoc($res)) {
                            ?>
                            <tr>
                                <td><?php echo $row['book_id_code']; ?></td>
                                <td><?php echo $row['member_id_code']; ?></td>
                                <td><?php echo date('d M Y', strtotime($row['issue_date'])); ?></td>
                                <td><?php echo date('d M Y', strtotime($row['due_date'])); ?></td>
                                <td><span class="status issued">Issued</span></td>
                                <td>
                                    <a href="transactions.php?return_id=<?php echo $row['id']; ?>&b_id=<?php echo $row['book_id_code']; ?>" 
                                       class="add-btn" style="padding: 5px 10px; background: #e67e22; text-decoration:none; font-size:0.8rem;">
                                       Mark Return
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center;'>Koi pending transaction nahi hai.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="issueModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-book-reader"></i> Issue a Book</h3>
                <span class="close-btn" onclick="closeIssueModal()">&times;</span>
            </div>
            <form class="modal-form" action="transactions.php" method="POST">
                <div class="input-group">
                    <label>Member ID</label>
                    <input type="text" name="m_id" placeholder="e.g. #M1001" required>
                </div>
                <div class="input-group">
                    <label>Book ID</label>
                    <input type="text" name="b_id" placeholder="e.g. #B1001" required>
                </div>
                <div class="row">
                    <div class="input-group">
                        <label>Issue Date</label>
                        <input type="date" name="issue_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="input-group">
                        <label>Due Date</label>
                        <input type="date" name="due_date" required>
                    </div>
                </div>
                <button type="submit" name="confirm_issue" class="save-btn" style="background: #e67e22;">Confirm Issue</button>
            </form>
        </div>
    </div>

    <script>
        function openIssueModal() { document.getElementById("issueModal").style.display = "block"; }
        function closeIssueModal() { document.getElementById("issueModal").style.display = "none"; }
        window.onclick = (e) => { if (e.target == document.getElementById("issueModal")) closeIssueModal(); }
    </script>

</body>
</html>
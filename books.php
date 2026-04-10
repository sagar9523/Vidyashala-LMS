<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';
// 2. Add Book Logic (Jab form submit ho)
if (isset($_POST['submit_book'])) {
    $title = mysqli_real_escape_string($conn, $_POST['book_title']);
    $author = mysqli_real_escape_string($conn, $_POST['author_name']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $id_code = mysqli_real_escape_string($conn, $_POST['book_id']);

    // Nayi book hamesha 'Available' status ke saath insert hogi
    $sql = "INSERT INTO books (book_id_code, title, author, category, status) 
            VALUES ('$id_code', '$title', '$author', '$category', 'Available')";

    if (mysqli_query($conn, $sql)) {
        header("Location: books.php?msg=success");
        exit();
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Books Inventory - Vidyashala</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

    <nav class="navbar">
        <div class="logo">Vidyashala</div>
        <ul class="nav-links">
            <li><a href="index.php">Home</a></li>
            <li><a href="books.php" class="active">Books</a></li>
            <li><a href="members.php">Members</a></li>
            <li><a href="transactions.php">Issue/Return</a></li>
            <li><a href="login.php" class="login-btn">Admin Login</a></li>
        </ul>
    </nav>

    <div id="bookModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-plus-circle"></i> Add New Book</h3>
                <span class="close-btn">&times;</span>
            </div>
            <form class="modal-form" action="books.php" method="POST">
                <div class="input-group">
                    <label>Book Title</label>
                    <input type="text" name="book_title" placeholder="Enter book name" required>
                </div>
                <div class="input-group">
                    <label>Author Name</label>
                    <input type="text" name="author_name" placeholder="Enter author name" required>
                </div>
                <div class="row">
                    <div class="input-group">
                        <label>Category</label>
                        <select name="category" required>
                            <option value="Fiction">Fiction</option>
                            <option value="Science">Science</option>
                            <option value="History">History</option>
                            <option value="Technology">Technology</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Book ID</label>
                        <input type="text" name="book_id" placeholder="e.g. #B101" required>
                    </div>
                </div>
                <button type="submit" name="submit_book" class="save-btn">Save Book</button>
            </form>
        </div>
    </div>

    <div class="container">
        <div class="page-header">
            <h2><i class="fas fa-book"></i> Books Inventory</h2>
            <button class="add-btn"><i class="fas fa-plus"></i> Add New Book</button>
        </div>

        <div class="filter-bar">
            <input type="text" id="searchInput" placeholder="Search by Book Name or Author...">
            <select>
                <option>All Categories</option>
                <option>Fiction</option>
                <option>Science</option>
                <option>History</option>
            </select>
        </div>

        <div class="table-wrapper">
            <table class="books-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Book Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Database se books lekar table mein dikhana
                    $res = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC");
                    
                    if(mysqli_num_rows($res) > 0) {
                        while ($row = mysqli_fetch_assoc($res)) {
                            $statusClass = strtolower($row['status']); 
                            ?>
                            <tr>
                                <td><?php echo $row['book_id_code']; ?></td>
                                <td><?php echo $row['title']; ?></td>
                                <td><?php echo $row['author']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><span class="status <?php echo $statusClass; ?>"><?php echo $row['status']; ?></span></td>
                                <td>
                                    <button class="edit-icon" title="Edit"><i class="fas fa-edit"></i></button>
                                    
                                    <a href="delete_book.php?id=<?php echo $row['id']; ?>" 
                                       class="delete-icon" 
                                       style="color: #ff4d4d; margin-left: 10px;"
                                       onclick="return confirm('Kya aap sach mein is book ko delete karna chahte hain?');">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center;'>Inventory khali hai. Nayi book add karein!</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        const modal = document.getElementById("bookModal");
        const btn = document.querySelector(".add-btn");
        const span = document.querySelector(".close-btn");

        // Modal open/close logic
        btn.onclick = () => modal.style.display = "block";
        span.onclick = () => modal.style.display = "none";
        window.onclick = (event) => {
            if (event.target == modal) modal.style.display = "none";
        }

        // Simple Search Filter
        document.getElementById('searchInput').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('.books-table tbody tr');
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
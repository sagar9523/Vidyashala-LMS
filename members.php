<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'config.php';

// 2. Add Member Logic
if (isset($_POST['register_member'])) {
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    
    // Automatic unique Member ID generate karna (e.g. #M4523)
    $m_id = "#M" . rand(1000, 9999); 

    $sql = "INSERT INTO members (member_id_code, full_name, email, category, phone, status) 
            VALUES ('$m_id', '$name', '$email', '$category', '$phone', 'Active')";

    if (mysqli_query($conn, $sql)) {
        header("Location: members.php?msg=success");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Management - Vidyashala</title>
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
            <li><a href="members.php" class="active">Members</a></li>
            <li><a href="transactions.php">Issue/Return</a></li>
            <li><a href="login.php" class="login-btn">Admin Login</a></li>
        </ul>
    </nav>

    <div class="container">
        <div class="page-header">
            <h2><i class="fas fa-users"></i> Library Members</h2>
            <button class="add-btn" onclick="openMemberModal()"><i class="fas fa-user-plus"></i> Add New Member</button>
        </div>

        <div class="filter-bar">
            <input type="text" id="memberSearch" placeholder="Search by Name, ID or Phone...">
            <select id="categoryFilter">
                <option value="">All Categories</option>
                <option value="Student">Student</option>
                <option value="Faculty">Faculty</option>
                <option value="Staff">Staff</option>
            </select>
        </div>

        <div class="table-wrapper">
            <table class="books-table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Member ID</th>
                        <th>Category</th>
                        <th>Joined Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Database se members fetch karke dikhana
                    $res = mysqli_query($conn, "SELECT * FROM members ORDER BY id DESC");
                    
                    if(mysqli_num_rows($res) > 0) {
                        while($row = mysqli_fetch_assoc($res)) {
                            // Avatar ke liye initials (e.g. Rahul Kumar -> RK)
                            $words = explode(" ", $row['full_name']);
                            $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ""));
                            
                            $statusClass = ($row['status'] == 'Active') ? 'available' : 'issued';
                    ?>
                    <tr>
                        <td class="user-info">
                            <div class="avatar"><?php echo $initials; ?></div>
                            <span><?php echo $row['full_name']; ?></span>
                        </td>
                        <td><?php echo $row['member_id_code']; ?></td>
                        <td><?php echo $row['category']; ?></td>
                        <td><?php echo date('d M Y', strtotime($row['joined_date'])); ?></td>
                        <td><span class="status <?php echo $statusClass; ?>"><?php echo $row['status']; ?></span></td>
                        <td>
                            <button class="edit-icon" title="Edit"><i class="fas fa-edit"></i></button>
                            
                            <a href="delete_member.php?id=<?php echo $row['id']; ?>" 
                               class="delete-icon" 
                               onclick="return confirm('Kya aap is member ko hatana chahte hain?');"
                               style="color: #ff4d4d; margin-left: 10px;">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php 
                        } 
                    } else {
                        echo "<tr><td colspan='6' style='text-align:center;'>Koi member nahi mila.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div id="memberModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-user-circle"></i> Register New Member</h3>
                <span class="close-btn" onclick="closeMemberModal()">&times;</span>
            </div>
            <form class="modal-form" action="members.php" method="POST">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="full_name" placeholder="e.g. Priya Singh" required>
                </div>
                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="priya@example.com">
                </div>
                <div class="row">
                    <div class="input-group">
                        <label>Category</label>
                        <select name="category">
                            <option value="Student">Student</option>
                            <option value="Faculty">Faculty</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Phone No.</label>
                        <input type="text" name="phone" placeholder="+91 00000 00000">
                    </div>
                </div>
                <button type="submit" name="register_member" class="save-btn">Register Member</button>
            </form>
        </div>
    </div>

    <script>
        // Modal functions
        function openMemberModal() { document.getElementById("memberModal").style.display = "block"; }
        function closeMemberModal() { document.getElementById("memberModal").style.display = "none"; }
        window.onclick = (e) => { if (e.target == document.getElementById("memberModal")) closeMemberModal(); }

        // Live Search Filter
        document.getElementById('memberSearch').addEventListener('keyup', function() {
            let filter = this.value.toLowerCase();
            let rows = document.querySelectorAll('.books-table tbody tr');
            rows.forEach(row => {
                row.style.display = row.innerText.toLowerCase().includes(filter) ? '' : 'none';
            });
        });
    </script>
</body>
</html>
<?php
// Database connection
include 'db.php';

// Fetch all users from the database
$sql = "SELECT * FROM Users";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Users</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Increase the width of the phone number column */
        .phone-column {
            width: 180px; /* Adjust the width as needed */
        }

        /* Align 'Add New User' button to the top right */
        .add-new-btn {
            float: right;
            margin-bottom: 15px;
        }

        /* Table row hover effect */
        tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>All Users</h2>
    <a href="add-user.php" class="btn btn-success add-new-btn">Add New User</a>
     <!-- Back to Dashboard Button -->
     <a href="dashboard.php" class="btn btn-secondary add-new-btn mr-2">Back to Dashboard</a>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th class="phone-column">Phone Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['UserID'] . "</td>";
                    echo "<td>" . $row['FirstName'] . "</td>";
                    echo "<td>" . $row['LastName'] . "</td>";
                    echo "<td>" . $row['Email'] . "</td>";
                    echo "<td>" . $row['PhoneNumber'] . "</td>";
                    echo "<td>
                            <a href='edit-user.php?id=" . $row['UserID'] . "' class='btn btn-primary btn-sm '>Edit</a>
                            <a href='delete-user.php?delete_id=" . $row['UserID'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this user?\")'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center'>No users found</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php
$conn->close();
?>

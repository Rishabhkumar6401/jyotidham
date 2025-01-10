<?php
// Database connection
include 'db.php';

// Check if a delete request is made
if (isset($_GET['delete_id'])) {
    $userID = $_GET['delete_id'];

    // Prepare the DELETE query
    $sql = "DELETE FROM Users WHERE UserID = ?";

    // Use prepared statements to prevent SQL injection
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $userID); // "i" denotes integer type
        $stmt->execute();

        // Check if the user was deleted
        if ($stmt->affected_rows > 0) {
            // Redirect back to the users page with a success message
            header("Location: view-user.php");
        } else {
            // Redirect back with an error message
            header("Location: view-user.php");
        }

        $stmt->close();
    } else {
        // In case of query preparation failure
        echo "Error preparing statement: " . $conn->error;
    }
}

// Fetch all users from the database
$sql = "SELECT * FROM Users";
$result = $conn->query($sql);
?>

<?php
// Database connection
include 'db.php';

// Check if 'id' is passed in the URL
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Prepare the SQL query to delete the event
    $sql = "DELETE FROM events WHERE id = ?";

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    
    // Bind the parameter
    $stmt->bind_param("i", $event_id);

    // Execute the query
    if ($stmt->execute()) {
        // Redirect back to the events list page after successful deletion
        header("Location: show-event.php");
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error deleting event: " . $conn->error . "</div>";
    }

    // Close the statement
    $stmt->close();
} else {
    echo "<div class='alert alert-danger'>Event ID is missing!</div>";
}

// Close the database connection
$conn->close();
?>

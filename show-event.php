<?php
// Database connection
include 'db.php';

// Fetch all events from the database
$sql = "SELECT * FROM events";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Events</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Increase the width of the event date column */
        .event-date-column {
            width: 200px; /* Adjust the width as needed */
        }

        /* Align 'Add New Event' button to the top right */
        .add-new-btn {
            float: right;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>All Events</h2>
    <a href="add-event.php" class="btn btn-success add-new-btn ">Add New Event</a>
     <!-- Back to Dashboard Button -->
     <a href="dashboard.php" class="btn btn-secondary add-new-btn mr-2">Back to Dashboard</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Day</th>
                <th class="event-date-column">Event Date</th>
                <th>Event Time</th>
                <th>Event Name</th>
                <th>Organizer</th>
                <th>Venue</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['id'] . "</td>";
                    echo "<td>" . $row['day'] . "</td>";
                    echo "<td>" . $row['event_date'] . "</td>";
                    echo "<td>" . $row['event_time'] . "</td>";
                    echo "<td>" . $row['event_name'] . "</td>";
                    echo "<td>" . $row['organizer'] . "</td>";
                    echo "<td>" . $row['event_venue'] . "</td>";
                    echo "<td>
                            <a href='edit-event.php?id=" . $row['id'] . "' class='btn btn-primary btn-sm mb-2'>Edit</a>
                            <a href='delete-event.php?id=" . $row['id'] . "' class='btn btn-danger btn-sm' onclick='return confirm(\"Are you sure you want to delete this event?\")'>Delete</a>
                          </td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='8' class='text-center'>No events found</td></tr>";
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

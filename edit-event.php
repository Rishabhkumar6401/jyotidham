<?php
// Database connection
include 'db.php';

// Fetch event ID from URL parameter
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Fetch the event data from the database
    $sql = "SELECT * FROM events WHERE id = '$event_id'";

    // Execute query and check if it returns a valid result
    $result = $conn->query($sql);

    if ($result) {
        $event = $result->fetch_assoc();

        if (!$event) {
            echo "<div class='alert alert-danger'>Event not found!</div>";
            exit();
        }
    } else {
        // Print SQL error if query fails
        echo "<div class='alert alert-danger'>Error executing query: " . $conn->error . "</div>";
        exit();
    }
} else {
    echo "<div class='alert alert-danger'>Event ID is missing!</div>";
    exit();
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $day = $_POST['day'];
    $event_date = $_POST['event_date'];
    $event_time = $_POST['event_time'];
    $event_end_time = $_POST['event_end_time'];
    $time_zone = $_POST['time_zone'];
    $event_name = $_POST['event_name'];
    $event_description = $_POST['event_description'];
    $organizer = $_POST['organizer'];
    $event_venue = $_POST['event_venue'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;

    // Update event in the database
    $sql = "UPDATE events SET 
    day = '$day', event_date = '$event_date', event_time = '$event_time', 
    event_end_time = '$event_end_time', time_zone = '$time_zone', 
    event_name = '$event_name', event_description = '$event_description', 
    organizer = '$organizer', event_venue = '$event_venue', 
    latitude = '$latitude', longitude = '$longitude', is_featured = '$is_featured' 
    WHERE id = '$event_id'";  // Use 'id' here

    if ($conn->query($sql) === TRUE) {
        echo "<div class='alert alert-success'>Event updated successfully</div>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'show-event.php';
                }, 500); // Redirect after 1 second
              </script>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="max-width: 500px; border: 3px solid blue; border-radius: 15px">
    <h2>Edit Event</h2>
    <form action="edit-event.php?id=<?php echo $event['id']; ?>" method="post">
        <!-- Day Dropdown -->
        <div class="form-group">
            <label for="day">Day</label>
            <select class="form-control" id="day" name="day" required>
                <option value="">Select Day</option>
                <option value="Monday" <?php echo $event['day'] == 'Monday' ? 'selected' : ''; ?>>Monday</option>
                <option value="Tuesday" <?php echo $event['day'] == 'Tuesday' ? 'selected' : ''; ?>>Tuesday</option>
                <option value="Wednesday" <?php echo $event['day'] == 'Wednesday' ? 'selected' : ''; ?>>Wednesday</option>
                <option value="Thursday" <?php echo $event['day'] == 'Thursday' ? 'selected' : ''; ?>>Thursday</option>
                <option value="Friday" <?php echo $event['day'] == 'Friday' ? 'selected' : ''; ?>>Friday</option>
                <option value="Saturday" <?php echo $event['day'] == 'Saturday' ? 'selected' : ''; ?>>Saturday</option>
                <option value="Sunday" <?php echo $event['day'] == 'Sunday' ? 'selected' : ''; ?>>Sunday</option>
            </select>
        </div>

        <!-- Date Picker -->
        <div class="form-group">
            <label for="date">Date</label>
            <input type="date" class="form-control" id="date" name="event_date" value="<?php echo $event['event_date']; ?>" required>
        </div>

        <div class="form-group">
    <label for="event_time">Event Start Time</label>
    <select class="form-control" id="event_time" name="event_time" required>
        <option value="">Select Start Time</option>
        <?php
        // Convert the event_time from 24-hour format to 12-hour format with AM/PM
        function convertTo12HourFormat($time) {
            return date("h:i A", strtotime($time)); // Convert to 12-hour format with AM/PM
        }

        // Loop for generating times from 12:00 AM to 11:30 PM
        $times = ['AM', 'PM']; // Array for AM/PM
        foreach ($times as $period) {
            for ($hour = 1; $hour <= 12; $hour++) {
                $hour_formatted = str_pad($hour, 2, '0', STR_PAD_LEFT);
                $time_12hr_1 = convertTo12HourFormat("$hour_formatted:00:00 $period"); // 12-hour format with AM/PM
                $time_12hr_2 = convertTo12HourFormat("$hour_formatted:30:00 $period"); // 12-hour format with AM/PM
                
                // Checking the stored event time in DB and selecting it
                $selected_1 = $time_12hr_1 == convertTo12HourFormat($event['event_time']) ? 'selected' : '';
                $selected_2 = $time_12hr_2 == convertTo12HourFormat($event['event_time']) ? 'selected' : '';

                echo "<option value='$time_12hr_1' $selected_1>$time_12hr_1</option>";
                echo "<option value='$time_12hr_2' $selected_2>$time_12hr_2</option>";
            }
        }
        ?>
    </select>
</div>

<!-- Event End Time Dropdown -->
<!-- Event End Time Dropdown -->
<div class="form-group">
    <label for="event_end_time">Event End Time</label>
    <select class="form-control" id="event_end_time" name="event_end_time" required>
        <option value="">Select End Time</option>
        <?php
        // Convert the event_end_time from 24-hour format to 12-hour format with AM/PM
        $start_time = strtotime($event['event_time']);
        $event_end_time = convertTo12HourFormat($event['event_end_time']); // Get the event's end time in 12-hour format

        // Loop through possible end times, starting from start time
        for ($hour = 1; $hour <= 12; $hour++) {
            foreach (['AM', 'PM'] as $period) {
                $formatted_hour = str_pad($hour, 2, '0', STR_PAD_LEFT);
                $end_time = strtotime("$formatted_hour:00 $period");

                // Ensure end time is later than start time
                if ($end_time > $start_time) {
                    $end_time_12hr = date("h:i A", $end_time); // Format the time to 12-hour format with AM/PM
                    $selected = ($end_time_12hr == $event_end_time) ? 'selected' : ''; // Compare with event's end time

                    echo "<option value='$end_time_12hr' $selected>$end_time_12hr</option>";

                    // Adding 30 minutes to the end time
                    $end_time_30min = strtotime("$formatted_hour:30 $period");
                    $end_time_12hr_30 = date("h:i A", $end_time_30min);

                    // Ensure the 30-min end time is selected if it matches
                    $selected_30 = ($end_time_12hr_30 == $event_end_time) ? 'selected' : '';
                    echo "<option value='$end_time_12hr_30' $selected_30>$end_time_12hr_30</option>";
                }
            }
        }
        ?>
    </select>
</div>




        <!-- Time Zone Dropdown -->
        <div class="form-group">
            <label for="time_zone">Time Zone</label>
            <select class="form-control" id="time_zone" name="time_zone" required>
                <option value="IST" <?php echo $event['time_zone'] == 'IST' ? 'selected' : ''; ?>>IST (Indian Standard Time)</option>
                <option value="EST" <?php echo $event['time_zone'] == 'EST' ? 'selected' : ''; ?>>EST (Eastern Standard Time)</option>
                <option value="EDT" <?php echo $event['time_zone'] == 'EDT' ? 'selected' : ''; ?>>EDT (Eastern Daylight Time)</option>
                <option value="PST" <?php echo $event['time_zone'] == 'PST' ? 'selected' : ''; ?>>PST (Pacific Standard Time)</option>
                <option value="GMT" <?php echo $event['time_zone'] == 'GMT' ? 'selected' : ''; ?>>GMT (Greenwich Mean Time)</option>
            </select>
        </div>

        <!-- Event Name -->
        <div class="form-group">
            <label for="event_name">Event Name</label>
            <input type="text" class="form-control" id="event_name" name="event_name" value="<?php echo $event['event_name']; ?>" required>
        </div>

        <!-- Event Description -->
        <div class="form-group">
            <label for="event_description">Event Description</label>
            <textarea class="form-control" id="event_description" name="event_description" rows="3" required><?php echo $event['event_description']; ?></textarea>
        </div>

        <!-- Organizer -->
        <div class="form-group">
            <label for="organizer">Organizer</label>
            <input type="text" class="form-control" id="organizer" name="organizer" value="<?php echo $event['organizer']; ?>" required>
        </div>

        <!-- Venue -->
        <div class="form-group">
            <label for="event_venue">Event Venue</label>
            <input type="text" class="form-control" id="event_venue" name="event_venue" value="<?php echo $event['event_venue']; ?>" required>
        </div>

        <!-- Hidden Latitude and Longitude -->
        <input type="hidden" id="latitude" name="latitude" value="<?php echo $event['latitude']; ?>">
        <input type="hidden" id="longitude" name="longitude" value="<?php echo $event['longitude']; ?>">

        <!-- Is Featured Dropdown -->
        <div class="form-group">
            <label for="is_featured">Is Featured</label>
            <select class="form-control" id="is_featured" name="is_featured" required>
                <option value="0" <?php echo $event['is_featured'] == 0 ? 'selected' : ''; ?>>No</option>
                <option value="1" <?php echo $event['is_featured'] == 1 ? 'selected' : ''; ?>>Yes</option>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Update Event</button>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<!-- Google Maps and Address Autocomplete -->
<script>
    let map;
    let marker;
    let autocomplete;

    function initMap() {
        const defaultLocation = { lat: <?php echo $event['latitude']; ?>, lng: <?php echo $event['longitude']; ?> };

        map = new google.maps.Map(document.getElementById('map'), {
            center: defaultLocation,
            zoom: 15
        });

        marker = new google.maps.Marker({
            position: defaultLocation,
            map: map,
            draggable: true
        });

        marker.addListener('dragend', function () {
            const lat = marker.getPosition().lat();
            const lng = marker.getPosition().lng();
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        });

        autocomplete = new google.maps.places.Autocomplete(document.getElementById('event_venue'), {
            types: ['geocode'],
            componentRestrictions: { country: 'ca' }
        });

        autocomplete.addListener('place_changed', function () {
            const place = autocomplete.getPlace();
            if (place.geometry) {
                const location = place.geometry.location;
                map.setCenter(location);
                map.setZoom(15);
                marker.setPosition(location);
                document.getElementById('latitude').value = location.lat();
                document.getElementById('longitude').value = location.lng();
            }
        });
    }

    window.onload = initMap;
</script>
</body>
</html>

<?php
include 'db.php';

// Check if the user ID is provided in the query string
if (isset($_GET['id'])) {
    $userId = $_GET['id'];

    // Fetch existing user data from the database
    $sql = "SELECT * FROM Users WHERE UserID = $userId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Fetch the user data
        $user = $result->fetch_assoc();
    } else {
        echo "<div class='alert alert-danger mt-3'>User not found.</div>";
        exit;
    }
} else {
    echo "<div class='alert alert-danger mt-3'>User ID not specified.</div>";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $conn->real_escape_string($_POST['firstName']);
    $lastName = $conn->real_escape_string($_POST['lastName']);
    $email = $conn->real_escape_string($_POST['email']);
    $phoneNumber = $conn->real_escape_string($_POST['phoneNumber']);
    $address = $conn->real_escape_string($_POST['address']);

    // Update user data in the database
    $updateSql = "UPDATE Users SET 
        FirstName = '$firstName', 
        LastName = '$lastName', 
        Email = '$email', 
        PhoneNumber = '$phoneNumber', 
        Address = '$address' 
        WHERE UserID = $userId";

    if ($conn->query($updateSql) === TRUE) {
        echo "<div class='alert alert-success mt-3'>User updated successfully</div>";
        echo "<script>
                setTimeout(function() {
                    window.location.href = 'view-user.php'; 
                }, 500);
              </script>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Error: " . $conn->error . "</div>";
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="margin-top:4rem !important; margin-bottom:4rem !important; max-width: 500px !important; border: 3px solid blue; border-radius:15px">
    <h2>Edit User</h2>
    <form action="edit-user.php?id=<?php echo $user['UserID']; ?>" method="POST" id="userForm" style="margin-top:2rem !important; margin-bottom:2rem !important;">
        <!-- First Name -->
        <div class="mb-3">
            <label for="firstName" class="form-label">First Name</label>
            <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name" value="<?php echo $user['FirstName']; ?>" required>
        </div>

        <!-- Last Name -->
        <div class="mb-3">
            <label for="lastName" class="form-label">Last Name</label>
            <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last Name" value="<?php echo $user['LastName']; ?>" required>
        </div>

        <!-- Email with Dynamic Validation -->
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo $user['Email']; ?>" required>
            <div id="emailFeedback" class="invalid-feedback">
                Please enter a valid email address.
            </div>
        </div>

        <!-- Phone Number with Country Code -->
        <div class="mb-3">
            <label for="phone" class="form-label px-3">Phone Number</label>
            <input type="tel" id="phoneNumber" name="phoneNumber" class="form-control" value="<?php echo $user['PhoneNumber']; ?>" required>
            <div id="phoneFeedback" class="invalid-feedback">
                Please enter a valid phone number.
            </div>
        </div>

        <!-- Address Fields with Autocomplete -->
        <div class="mb-3">
            <label for="address" class="form-label">Address</label>
            <input type="text" class="form-control" id="address" name="address" placeholder="Start typing your address" value="<?php echo $user['Address']; ?>" required>
        </div>

        <button type="submit" class="btn btn-primary">Submit</button>
        <a href="./dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>
</div>

<!-- Google Maps API for Autocomplete -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBM1nAywoajfBnPLSqZn0z5wvUNj2ZYhF0&libraries=places"></script>
<script>
function initAutocomplete() {
    var addressField = document.getElementById('address');

    // Initialize Google Maps Autocomplete
    var autocomplete = new google.maps.places.Autocomplete(addressField, {
        types: ['geocode'],
        componentRestrictions: { country: 'ca' } // Restrict to Canada
    });

    autocomplete.addListener('place_changed', function () {
        var place = autocomplete.getPlace();
        console.log('Selected address:', place.formatted_address);
    });
}

// Initialize the autocomplete on page load
google.maps.event.addDomListener(window, 'load', initAutocomplete);
</script>

<script>
// Email validation
document.getElementById("email").addEventListener("input", function () {
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const emailInput = this;
    if (!emailPattern.test(emailInput.value)) {
        emailInput.classList.add('is-invalid');
    } else {
        emailInput.classList.remove('is-invalid');
    }
});
</script>

<!-- intl-tel-input initialization and functionality -->
<script>
    var phoneInputField = document.querySelector("#phoneNumber");

    var iti = window.intlTelInput(phoneInputField, {
        initialCountry: "ca",
        geoIpLookup: function(callback) {
            fetch('https://ipinfo.io/json')
                .then(function(response) { return response.json(); })
                .then(function(data) { callback(data.country); })
                .catch(function() { callback("us"); });
        },
        separateDialCode: true,
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
    });

    document.querySelector("form").addEventListener("submit", function(event) {
        if (!iti.isValidNumber()) {
            event.preventDefault();
            phoneInputField.classList.add("is-invalid");
            document.getElementById("phoneFeedback").textContent = "Please enter a valid phone number.";
        } else {
            phoneInputField.classList.remove("is-invalid");

            let hiddenPhoneInput = document.createElement("input");
            hiddenPhoneInput.type = "hidden";
            hiddenPhoneInput.name = "fullPhoneNumber";
            hiddenPhoneInput.value = iti.getNumber();

            this.appendChild(hiddenPhoneInput);
        }
    });
</script>
</body>
</html>

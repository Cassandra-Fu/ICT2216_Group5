<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'dbconntest.php';

// Check if the user has the 'customer' role
if (!isset($_SESSION["Role"]) || $_SESSION["Role"] !== 'customer') {
    header("location: unauthorized_adminstaff.php");
    exit;
}

// Check if booking_id is provided in the URL
if (!isset($_GET['booking_id'])) {
    die("Booking ID not provided.");
}

$booking_id = $_GET['booking_id'];

// Retrieve CustomerID from session
if (!isset($_SESSION['id'])) {
    die("Customer ID not found in session.");
}
$customerID = $_SESSION['id'];

// Fetch unavailable date ranges
$unavailable_dates = [];
$availability_query = "SELECT StartDate, EndDate FROM availability WHERE Deleted_At IS NULL";
$availability_result = $conn->query($availability_query);
while ($row = $availability_result->fetch_assoc()) {
    $unavailable_dates[] = $row;
}

// Fetch booking details for the specific booking_id and customerID
$sql = "SELECT b.*, s.ServiceName FROM booking b JOIN service s ON b.ServiceID = s.ID WHERE b.ID = '$booking_id' AND b.CustomerID = '$customerID'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    // Fetch booking details
    $booking = $result->fetch_assoc();

    // Handle form submission to update booking
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Get updated form data
        $dropoff_date = $conn->real_escape_string($_POST['boarding_dropoffdate']);
        $pickup_date = $conn->real_escape_string($_POST['boarding_pickupdate']);
        $food = isset($_POST['food']) && $_POST['food'] === 'Yes' ? 1 : 0;
        $comments = $conn->real_escape_string($_POST['comments']);

        if ($booking['ServiceName'] !== 'Boarding' && $dropoff_date !== $pickup_date) {
            echo "<script>alert('For services other than Boarding, the drop-off date and pick-up date must be the same.');</script>";
        } else {
            // Update the booking in the database
            $update_sql = "UPDATE booking SET DropOffDate = '$dropoff_date', PickUpDate = '$pickup_date', Food = '$food', Remarks = '$comments', Status = 'Modified' WHERE ID = '$booking_id'";

            if ($conn->query($update_sql) === TRUE) {
                // Redirect to my bookings page or show a success message
                header('Location: mybookings.php');
                exit;
            } else {
                echo "Error updating booking: " . $conn->error;
            }
        }
    }
} else {
    die("Booking not found or unauthorized access.");
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <?php include "head.inc.php"; ?>
        <link rel="stylesheet" href="css/edit_booking.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="js/inactivity.js"></script>
    </head>
    <body>
        <?php include "topbar.inc.php"; ?>
        <?php include "nav.inc.php"; ?>

        <div class="container mt-5">
            <h2>Edit Booking</h2>
            <div class="row" id="edit_container">
                <div class="col-md-6">
                    <form method="post" action="" onsubmit="return validateDates()">
                        <div class="form-group">
                            <label for="boarding_dropoffdate">Drop-off Date:</label>
                            <input type="date" id="boarding_dropoffdate" name="boarding_dropoffdate" class="flatpickr" value="<?php echo htmlspecialchars($booking['DropOffDate']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="boarding_pickupdate">Pick-up Date:</label>
                            <input type="date" id="boarding_pickupdate" name="boarding_pickupdate" class="flatpickr" value="<?php echo htmlspecialchars($booking['PickUpDate']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Complimentary Food:</label><br>
                            <input type="radio" id="yes" name="food" value="Yes" <?php if ($booking['Food'] == 1) echo 'checked'; ?>>
                            <label for="yes">Yes</label><br>
                            <input type="radio" id="no" name="food" value="No" <?php if ($booking['Food'] == 0) echo 'checked'; ?>>
                            <label for="no">No</label>
                        </div>
                        <div class="form-group">
                            <label for="comments">Allergy / Comments / Remarks:</label>
                            <textarea id="comments" name="comments" rows="5" cols="50"><?php echo htmlspecialchars($booking['Remarks']); ?></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Booking</button>
                    </form>
                </div>
            </div>
        </div>

        <?php include "footer.inc.php"; ?>
        <a href="#" class="btn btn-lg btn-primary back-to-top"><i class="fa fa-angle-double-up"></i></a>

        <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
        <script src="lib/easing/easing.min.js"></script>
        <script src="lib/owlcarousel/owl.carousel.min.js"></script>
        <script src="lib/tempusdominus/js/moment.min.js"></script>
        <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
        <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>
        <script src="mail/jqBootstrapValidation.min.js"></script>
        <script src="mail/contact.js"></script>
        <script src="js/main.js"></script>

        <script>

            document.addEventListener('DOMContentLoaded', function () {
                const unavailableDates = <?php echo json_encode($unavailable_dates); ?>;

                function getDisabledDates(unavailableDates) {
                    let disabledDates = [];
                    unavailableDates.forEach(range => {
                        let start = new Date(range.StartDate);
                        let end = new Date(range.EndDate);
                        while (start <= end) {
                            disabledDates.push(start.toISOString().split('T')[0]);
                            start.setDate(start.getDate() + 1);
                        }
                    });
                    return disabledDates;
                }

                const disabledDates = getDisabledDates(unavailableDates);

                flatpickr(".flatpickr", {
                    disable: disabledDates,
                    dateFormat: "Y-m-d",
                    minDate: "today"
                });
            });

            function validateDates() {
                const dropoffDate = new Date(document.getElementById('boarding_dropoffdate').value);
                const pickupDate = new Date(document.getElementById('boarding_pickupdate').value);

                if (pickupDate <= dropoffDate) {
                    alert('Pick-up date must be after the drop-off date.');
                    return false;
                }

                // Check if both date fields are not empty
                if (!document.getElementById('boarding_dropoffdate').value) {
                    alert('Please ensure both drop-off and pick-up dates are selected.');
                    return false;
                }

                if (!document.getElementById('boarding_pickupdate').value) {
                    alert('Please ensure both drop-off and pick-up dates are selected.');
                    return false;
                }

                return true;
            }

        </script>
    </body>
</html>

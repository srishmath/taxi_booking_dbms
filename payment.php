<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

if (isset($_SESSION['booking_id']) && isset($_SESSION['distance'])) {
    $booking_id = $_SESSION['booking_id'];
    $distance = $_SESSION['distance'];
    $c_id = $_SESSION['c_id'];
    
    // Verify if the booking entry exists
    $verifyBooking = $conn->prepare("SELECT B_id FROM Booking WHERE B_id = ?");
    $verifyBooking->bind_param("i", $booking_id);
    $verifyBooking->execute();
    $bookingExists = $verifyBooking->get_result()->num_rows > 0;

    if ($bookingExists) {
        // Calculate fare based on distance
        $fareQuery = "SELECT CalculateFare(?) AS Cost";
        $fareStmt = $conn->prepare($fareQuery);
        $fareStmt->bind_param("d", $distance);
        $fareStmt->execute();
        $fareResult = $fareStmt->get_result();
        $cost = $fareResult->fetch_assoc()['Cost'];

        // Prepare the HTML output
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Payment Summary</title>
            <link rel="stylesheet" type="text/css" href="style.css">
        </head>
        <body>
            <div class="container">
                <h1>Payment Summary</h1>
                <p>Booking ID: <?php echo $booking_id; ?></p>
                <p>Distance: <?php echo $distance; ?> km</p>
                <p>Total Fare: $<?php echo $cost; ?></p>

                <?php
                // Insert payment into Payment table
                $paymentStmt = $conn->prepare("INSERT INTO Payment (B_id, Mode, Distance, Cost) VALUES (?, 'Cash', ?, ?)");
                $paymentStmt->bind_param("idd", $booking_id, $distance, $cost);

                if ($paymentStmt->execute()) {
                    echo "<p>Payment recorded successfully! Thank you for your payment.</p>";

                    // Update booking status to "Completed"
                    $updateBookingStatus = $conn->prepare("UPDATE Booking SET Status = 'Completed' WHERE B_id = ?");
                    $updateBookingStatus->bind_param("i", $booking_id);
                    if ($updateBookingStatus->execute()) {
                        echo "<p>Booking status has been updated to 'Completed'.</p>";

                        // Fetch the driver ID associated with the booking
                        $driverQuery = "SELECT D_id FROM Booking WHERE B_id = ?";
                        $driverStmt = $conn->prepare($driverQuery);
                        $driverStmt->bind_param("i", $booking_id);
                        $driverStmt->execute();
                        $driverResult = $driverStmt->get_result();

                        if ($driverResult->num_rows > 0) {
                            $driver_id = $driverResult->fetch_assoc()['D_id'];

                            // Set the driver's status to 'Available'
                            $updateDriverStatus = $conn->prepare("UPDATE Driver SET Status = 'Available' WHERE D_id = ?");
                            $updateDriverStatus->bind_param("i", $driver_id);
                            if ($updateDriverStatus->execute()) {
                                echo "<p>The driver's status has been updated to 'Available'.</p>";
                            } else {
                                echo "<p>Error updating driver status: " . $updateDriverStatus->error . "</p>";
                            }
                            echo "<form action='feedback.php' method='post'>
                                    <input type='hidden' name='c_id' value='$c_id'>
                                    <input type='hidden' name='d_id' value='$driver_id'>
                                    <button type='submit'>Provide Feedback</button>
                                  </form>";
                        } else {
                            echo "<p>Error: Driver not found for this booking.</p>";
                        }
                    } else {
                        echo "<p>Error updating booking status: " . $updateBookingStatus->error . "</p>";
                    }

                } else {
                    echo "<p>Error: " . $paymentStmt->error . "</p>";
                }
                ?>
            </div>
        </body>
        </html>
        <?php
    } else {
        echo "<p>Booking ID not found.</p>";
    }
} else {
    echo "<p>Booking details not available for payment.</p>";
}
?>

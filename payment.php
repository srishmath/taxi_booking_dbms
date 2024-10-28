<?php
session_start();
include 'db_connection.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if (isset($_SESSION['booking_id']) && isset($_GET['distance'])) {
        $booking_id = $_SESSION['booking_id'];
        $distance = $_GET['distance'];

        // Calculate fare based on distance
        $fareQuery = "SELECT CalculateFare(?) AS Cost";
        $fareStmt = $conn->prepare($fareQuery);
        $fareStmt->bind_param("d", $distance);
        $fareStmt->execute();
        $fareResult = $fareStmt->get_result();
        $cost = $fareResult->fetch_assoc()['Cost'];

        echo "<h1>Payment Summary</h1>";
        echo "<p>Booking ID: $booking_id</p>";
        echo "<p>Distance: $distance km</p>";
        echo "<p>Total Fare: $$cost</p>";

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
                } else {
                    echo "<p>Error: Driver not found for this booking.</p>";
                }
            } else {
                echo "<p>Error updating booking status: " . $updateBookingStatus->error . "</p>";
            }

            // Clear session variables related to booking
            unset($_SESSION['booking_id'], $_SESSION['distance']);
        } else {
            echo "<p>Error: " . $paymentStmt->error . "</p>";
        }
    } else {
        echo "<p>Booking details not available for payment.</p>";
    }
} else {
    header("location: login.php"); // Redirect to login if not logged in
    exit;
}
?>

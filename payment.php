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
            
            // Optional: Update booking status to "Paid" or similar if you want to track payment status
            $updateBookingStatus = $conn->prepare("UPDATE Booking SET Status = 'Completed' WHERE B_id = ?");
            $updateBookingStatus->bind_param("i", $booking_id);
            $updateBookingStatus->execute();
            
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

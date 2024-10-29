<?php
session_start();
include 'db_connection.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    if (isset($_SESSION['booking_id'])) {
        $booking_id = $_SESSION['booking_id'];

        // Fetch driver ID and customer ID associated with the booking
        $bookingQuery = "SELECT D_id, C_id FROM Booking WHERE B_id = ?";
        $bookingStmt = $conn->prepare($bookingQuery);
        $bookingStmt->bind_param("i", $booking_id);
        $bookingStmt->execute();
        $bookingResult = $bookingStmt->get_result();

        if ($bookingResult->num_rows > 0) {
            $bookingData = $bookingResult->fetch_assoc();
            $driver_id = $bookingData['D_id'];
            $c_id = $bookingData['C_id'];

            // Example feedback data (normally this would come from a form)
            $rating = $_POST['rating']; // Assume rating is sent from a form
            $comments = $_POST['comments']; // Assume comments are sent from a form

            // Insert feedback into the Feedback table using a nested query
            $feedbackInsertQuery = "INSERT INTO Feedback (C_id, D_id, Rating, Comments)
                                    VALUES (?, ?, ?, ?)";
            $feedbackStmt = $conn->prepare($feedbackInsertQuery);
            $feedbackStmt->bind_param("iiis", $c_id, $driver_id, $rating, $comments);

            if ($feedbackStmt->execute()) {
                echo "<p>Feedback submitted successfully!</p>";
            } else {
                echo "<p>Error submitting feedback: " . $feedbackStmt->error . "</p>";
            }
        } else {
            echo "<p>Booking not found.</p>";
        }
    } else {
        echo "<p>Booking details not available.</p>";
    }
} else {
    header("location: login.php"); // Redirect to login if not logged in
    exit;
}
?>

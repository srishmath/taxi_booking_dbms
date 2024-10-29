<?php
session_start();

if (isset($_POST['booking_id']) && isset($_POST['distance'])) {
    $_SESSION['booking_id'] = $_POST['booking_id'];
    $_SESSION['distance'] = $_POST['distance'];
    header("Location: payment.php");
    exit;
} else {
    echo "Error: Booking ID or distance not provided.";
}
?>

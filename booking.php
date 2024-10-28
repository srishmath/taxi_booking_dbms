<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Booking Page</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Booking Page</h2>
        <form action="booking_action.php" method="POST">
            <label for="carType">Car Type:</label>
            <select id="carType" name="carType" required>
                <option value="Mini">Mini</option>
                <option value="Sedan">Sedan</option>
                <option value="SUV">SUV</option>
            </select>
            <label for="distance">Distance (in km):</label>
            <input type="number" id="distance" name="distance" required>
            <label for="pickUp">Pickup Location:</label>
            <input type="text" id="pickUp" name="pickUp" required>
            <label for="dropOff">Drop-off Location:</label>
            <input type="text" id="dropOff" name="dropOff" required>
            <button type="submit">Book Now</button>
        </form>
    </div>
</body>
</html>

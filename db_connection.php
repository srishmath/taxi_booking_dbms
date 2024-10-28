<?php
$servername = "localhost";
$username = "root";
$password = ""; // Replace with your MySQL password if any
$dbname = "Taxi_Booking";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

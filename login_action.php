<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['Number']) && isset($_POST['Password'])) {
        $number = $_POST['Number'];
        $password = $_POST['Password'];

        // Prepare and execute SQL query
        $stmt = $conn->prepare("SELECT * FROM Customer WHERE Number = ? AND Password = ?");
        $stmt->bind_param("ss", $number, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        // Check if user exists
        if ($result->num_rows > 0) {
            // Fetch the customer data
            $customer = $result->fetch_assoc();

            $_SESSION['loggedin'] = true; // Set session variable
            $_SESSION['c_id'] = $customer['C_id']; // Store customer ID in session
            
            header("location: booking.php"); // Redirect to booking page
            exit;
        } else {
            echo "Invalid login credentials.";
        }
    } else {
        echo "Number or Password not set.";
    }
}
?>

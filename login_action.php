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

        // Check if the username (phone number) exists
        $stmt = $conn->prepare("SELECT * FROM Customer WHERE Number = ?");
        $stmt->bind_param("s", $number);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            // If username exists, check the password
            $customer = $result->fetch_assoc();
            if ($customer['Password'] === $password) {
                // Password is correct, log the user in
                $_SESSION['loggedin'] = true;
                $_SESSION['c_id'] = $customer['C_id'];
                header("location: booking.php");
                exit;
            } else {
                // Incorrect password
                $_SESSION['error'] = "Incorrect password.";
                header("location: login.php");
                exit;
            }
        } else {
            // Username does not exist
            $_SESSION['error'] = "Phone Number does not exist.";
            header("location: login.php");
            exit;
        }
    } else {
        $_SESSION['error'] = "Number or Password not set.";
        header("location: login.php");
        exit;
    }
}
?>

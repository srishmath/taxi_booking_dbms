<?php
session_start();
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        $carType = $_POST['carType'];
        $distance = $_POST['distance'];
        $pickUp = $_POST['pickUp'];
        $dropOff = $_POST['dropOff'];
        $c_id = $_SESSION['c_id']; // Customer ID from session
        
        // Find an available driver
        $driverQuery = "SELECT D_id FROM Driver WHERE Status = 'Available' LIMIT 1";
        $driverResult = $conn->query($driverQuery);

        if ($driverResult->num_rows > 0) {
            $driver = $driverResult->fetch_assoc();
            $driver_id = $driver['D_id'];

            // Use the CalculateFare function to get the cost
            $fareQuery = "SELECT CalculateFare(?) AS Cost";
            $fareStmt = $conn->prepare($fareQuery);
            $fareStmt->bind_param("d", $distance);
            $fareStmt->execute();
            $fareResult = $fareStmt->get_result();
            $cost = $fareResult->fetch_assoc()['Cost'];

            // Get the current time for the booking
            $currentTime = date('Y-m-d H:i:s');

            // Insert booking into the database with driver ID and time
            $stmt = $conn->prepare("INSERT INTO Booking (C_id, D_id, Pick_loc, Drop_loc, Status, Time, Distance) VALUES (?, ?, ?, ?, 'Booked', ?, ?)");
            $stmt->bind_param("iisssd", $c_id, $driver_id, $pickUp, $dropOff, $currentTime, $distance);
            
            if ($stmt->execute()) {
                // Update the driver's status to 'Not Available'
                $updateDriverQuery = "UPDATE Driver SET Status = 'Not Available' WHERE D_id = ?";
                $updateStmt = $conn->prepare($updateDriverQuery);
                $updateStmt->bind_param("i", $driver_id);
                $updateStmt->execute();

                echo "Booking successful! Driver ID $driver_id has been allocated to your ride.";
            } else {
                echo "Error: " . $stmt->error;
            }
        } else {
            echo "No available drivers at the moment.";
        }
    } else {
        header("location: login.php"); // Redirect to login if not logged in
        exit;
    }
}

if ($stmt->execute()) {
    // Update the driver's status to 'Not Available'
    $updateDriverQuery = "UPDATE Driver SET Status = 'Not Available' WHERE D_id = ?";
    $updateStmt = $conn->prepare($updateDriverQuery);
    $updateStmt->bind_param("i", $driver_id);
    $updateStmt->execute();

    // Store booking ID in session for payment
    $_SESSION['booking_id'] = $stmt->insert_id;
    $_SESSION['distance'] = $distance;

    echo "Booking successful! Driver ID $driver_id has been allocated to your ride.";
    echo "<form action='payment.php' method='get'>
            <input type='hidden' name='distance' value='$distance'>
            <button type='submit'>Pay Now</button>
          </form>";
} else {
    echo "Error: " . $stmt->error;
}



?>

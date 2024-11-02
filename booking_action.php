<?php
session_start();
include 'db_connection.php';

// Function to calculate fare based on distance
function CalculateFare($distance) {
    $ratePerKm = 10;
    $base_rate = 100;

    return $base_rate + (($distance - 1) * $ratePerKm);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
        $carType = $_POST['carType'];
        $distance = $_POST['distance'];
        $pickUp = $_POST['pickUp'];
        $dropOff = $_POST['dropOff'];
        $c_id = $_SESSION['c_id']; // Customer ID from session
        
        // Find an available driver and get their details
        $driverQuery = "
            SELECT 
                d.D_id, 
                d.D_Name AS Driver_Name, 
                d.D_Number AS Driver_Number, 
                d.Rating AS Driver_Rating
            FROM 
                Driver d 
            WHERE 
                d.Status = 'Available' 
            LIMIT 1";
        
        $driverResult = $conn->query($driverQuery);

        if ($driverResult->num_rows > 0) {
            $driver = $driverResult->fetch_assoc();
            $driver_id = $driver['D_id'];
            $driver_name = $driver['Driver_Name']; // Get driver name
            $driver_phone = $driver['Driver_Number']; // Get driver phone number
            $driver_rating = $driver['Driver_Rating']; // Get driver rating

            // Get the current time for the booking
            $currentTime = date('Y-m-d H:i:s');

            // Insert booking into the database with driver ID and time
            $stmt = $conn->prepare("INSERT INTO Booking (C_id, D_id, Pick_loc, Drop_loc, Status, Time, Distance) VALUES (?, ?, ?, ?, 'Booked', ?, ?)");
            $stmt->bind_param("iisssd", $c_id, $driver_id, $pickUp, $dropOff, $currentTime, $distance);
            
            if ($stmt->execute()) {
                // Store booking ID in session for payment
                $_SESSION['booking_id'] = $stmt->insert_id;
                $_SESSION['distance'] = $distance;

                // Update the driver's status to 'Not Available'
                $updateDriverQuery = "UPDATE Driver SET Status = 'Not Available' WHERE D_id = ?";
                $updateStmt = $conn->prepare($updateDriverQuery);
                $updateStmt->bind_param("i", $driver_id);
                $updateStmt->execute();

                // Calculate fare
                $fare = CalculateFare($distance);

                // Display booking details, driver details, and fare
                echo "<!DOCTYPE html>
                <html lang='en'>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <title>Booking Confirmation</title>
                    <style>
                        /* Inline CSS Styling */
                        body {
                            font-family: Arial, sans-serif;
                            background-color: #f4f4f4;
                            margin: 0;
                            padding: 0;
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            min-height: 100vh;
                        }

                        header {
                            width: 100%;
                            background-color: #333;
                            color: #fff;
                            padding: 15px 20px;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            position: relative;
                            box-sizing: border-box;
                        }

                        header h1 {
                            margin: 0;
                            font-size: 24px;
                            font-weight: bold;
                            text-align: center;
                        }

                        .header-button {
                            position: absolute;
                            right: 20px;
                            background-color: #5cb85c;
                            color: white;
                            padding: 10px 20px;
                            border: none;
                            border-radius: 5px;
                            cursor: pointer;
                            text-decoration: none;
                        }

                        .go-back-button {
                            position: absolute;
                            left: 20px;
                            background-color: #d9534f;
                            color: white;
                            padding: 10px 20px;
                            border: none;
                            border-radius: 5px;
                            cursor: pointer;
                            text-decoration: none;
                        }

                        .container {
                            width: 400px;
                            padding: 40px 30px;
                            background: white;
                            border-radius: 10px;
                            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                            box-sizing: border-box;
                            margin-top: 20px;
                        }

                        h2 {
                            text-align: center;
                            margin-bottom: 20px;
                        }

                        label {
                            display: block;
                            margin: 10px 0 5px;
                            text-align: left;
                        }

                        input, select {
                            width: calc(100% - 20px);
                            padding: 10px;
                            margin-bottom: 20px;
                            border: 1px solid #ccc;
                            border-radius: 5px;
                            box-sizing: border-box;
                        }

                        button {
                            width: 100%;
                            padding: 12px;
                            background: #5cb85c;
                            border: none;
                            color: white;
                            border-radius: 5px;
                            cursor: pointer;
                        }

                        button:hover {
                            background: #4cae4c;
                        }

                        .error-message {
                            color: red;
                            margin: -5px 0 15px;
                            text-align: center;
                            font-size: 14px;
                        }
                    </style>
                </head>
                <body>";
                
                echo "<header>
                        <h1>Taxi Service</h1>
                        <a href='view_bookings.php' class='header-button'>View My Bookings</a>
                        <a href='login.php' class='go-back-button'>Go Back to Login</a>
                      </header>";
                echo "<div class='container'>";
                echo "<h2>Booking Confirmation</h2>";
                echo "<p>Driver Name: $driver_name</p>";
                echo "<p>Driver Phone Number: $driver_phone</p>"; // Display driver details
                echo "<p>Pickup Location: $pickUp</p>";
                echo "<p>Drop-off Location: $dropOff</p>";
                echo "<p>Distance: $distance km</p>";
                echo "<p><strong>Fare: ₹$fare</strong></p>";
                echo "<form action='payment.php' method='get' style='text-align: center;'>
                        <input type='hidden' name='distance' value='$distance'>
                        <button type='submit' style='background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; display: block; margin: 0 auto;'>Pay Now</button>
                      </form>";
                echo "</div>";
                echo "</body></html>";
            } else {
                echo "<p class='error-message'>Error: " . $stmt->error . "</p>";
            }
        } else {
            echo "<p class='error-message'>No available drivers at the moment.</p>";
        }
    } else {
        header("location: login.php"); // Redirect to login if not logged in
        exit;
    }
}
?>

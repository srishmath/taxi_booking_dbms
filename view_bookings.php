<?php
session_start();
include 'db_connection.php';

if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    $c_id = $_SESSION['c_id']; // Customer ID from session
    
    // SQL query to fetch booking details
    $bookingsQuery = "
        SELECT 
            b.B_id, 
            b.Pick_loc, 
            b.Drop_loc, 
            b.Status AS Booking_Status, 
            b.Distance, 
            b.Time, 
            d.D_Name AS Driver_Name, 
            d.D_Number AS Driver_Number, 
            d.Rating AS Driver_Rating,
            c.Car_D AS Car_Type
        FROM 
            Booking b
        LEFT JOIN 
            Driver d ON b.D_id = d.D_id
        LEFT JOIN 
            Car c ON b.B_id = c.B_id
        WHERE 
            b.C_id = ?";
    
    $stmt = $conn->prepare($bookingsQuery);
    $stmt->bind_param("i", $c_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Start HTML output with CSS styling
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Your Bookings</title>
        <style>
            /* CSS Styling */
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

            .container {
                width: 90%;
                max-width: 1000px;
                padding: 20px;
                background: white;
                border-radius: 10px;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
                box-sizing: border-box;
                margin-top: 20px;
                text-align: center;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            th, td {
                padding: 10px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }

            th {
                background-color: #333;
                color: white;
            }

            button {
                padding: 5px 10px;
                background: #5cb85c;
                border: none;
                color: white;
                border-radius: 5px;
                cursor: pointer;
            }

            button:hover {
                background: #4cae4c;
            }

            p {
                text-align: center;
                font-size: 18px;
                color: #333;
            }
        </style>
    </head>
    <body>
        <header>
            <h1>Taxi Service</h1>
            <a href='login.php' class='header-button'>Logout</a>
        </header>
        <div class='container'>
            <h1>Your Bookings</h1>";
    
    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>Booking ID</th>
                    <th>Pickup Location</th>
                    <th>Drop Location</th>
                    <th>Status</th>
                    <th>Distance (km)</th>
                    <th>Time</th>
                    <th>Driver Name</th>
                    <th>Driver Number</th>
                    <th>Driver Rating</th>
                    <th>Car Type</th>
                    <th>Action</th>
                </tr>";
        
        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$row['B_id']}</td>
                    <td>{$row['Pick_loc']}</td>
                    <td>{$row['Drop_loc']}</td>
                    <td>{$row['Booking_Status']}</td>
                    <td>{$row['Distance']}</td>
                    <td>{$row['Time']}</td>
                    <td>{$row['Driver_Name']}</td>
                    <td>{$row['Driver_Number']}</td>
                    <td>{$row['Driver_Rating']}</td>
                    <td>{$row['Car_Type']}</td>";
            
            if ($row['Booking_Status'] === 'Booked') {
                echo "<td>
                        <form action='payment.php' method='get'>
                            <input type='hidden' name='booking_id' value='{$row['B_id']}'>
                            <input type='hidden' name='distance' value='{$row['Distance']}'>
                            <button type='submit'>Pay Now</button>
                        </form>
                      </td>";
            } else {
                echo "<td>Paid</td>";
            }
            
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p>You have no bookings.</p>";
    }

    echo "</div></body></html>";
} else {
    header("location: login.php"); // Redirect if not logged in
    exit;
}
?>

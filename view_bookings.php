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

    echo "<h1>Your Bookings</h1>";
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
} else {
    header("location: login.php"); // Redirect if not logged in
    exit;
}
?>

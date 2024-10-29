<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

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

?>

<!DOCTYPE html>
<html lang="en">
<head>
   
    
    <title>Your Bookings</title>
    
</head>
<body>
    <div class="container">
        <h1>Your Bookings</h1>
        <?php if ($result->num_rows > 0): ?>
            <table>
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
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['B_id']; ?></td>
                        <td><?php echo $row['Pick_loc']; ?></td>
                        <td><?php echo $row['Drop_loc']; ?></td>
                        <td><?php echo $row['Booking_Status']; ?></td>
                        <td><?php echo $row['Distance']; ?></td>
                        <td><?php echo $row['Time']; ?></td>
                        <td><?php echo $row['Driver_Name']; ?></td>
                        <td><?php echo $row['Driver_Number']; ?></td>
                        <td><?php echo $row['Driver_Rating']; ?></td>
                        <td><?php echo $row['Car_Type']; ?></td>
                        <td>
                            <?php if ($row['Booking_Status'] === 'Booked'): ?>
                                <form action='set_booking_id.php' method='post'>
                                    <input type='hidden' name='booking_id' value='<?php echo $row['B_id']; ?>'>
                                    <input type='hidden' name='distance' value='<?php echo $row['Distance']; ?>'>
                                    <button type='submit'>Pay Now</button>
                                </form>
                            <?php else: ?>
                                Paid
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>You have no bookings.</p>
        <?php endif; ?>
    </div>
</body>
</html>

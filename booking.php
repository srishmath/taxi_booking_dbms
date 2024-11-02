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
<body>

    <!-- Header with centered title, "Go Back to Login" button, and "View My Bookings" button -->
    <header>
        <a href="login.php" class="go-back-button">Go Back to Login</a>
        <h1>Taxi Service</h1>
        <a href="view_bookings.php" class="header-button">View My Bookings</a>
    </header>

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

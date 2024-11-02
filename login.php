<?php 
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Login</title>
    <style>
        /* CSS Styling */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            /* min-height: 100vh; */
        }

        header {
            width: 100%;
            background-color: #f4f4f4; /* Dark background for the header */
            color: #5cb85c; /* White text for contrast */
            padding: 15px 0; /* Padding for spacing */
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }

        .container {
            width: 400px;
            padding: 40px 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            box-sizing: border-box;
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

    <!-- Header with centered title -->
    <header>
        <h1>Taxi Service</h1>
    </header>

    <div class="container">
        <h2>Customer Login</h2>
        
        <form action="login_action.php" method="POST">
            <label for="Number">Phone Number:</label>
            <input type="text" id="Number" name="Number" required>
            <label for="Password">Password:</label>
            <input type="password" id="Password" name="Password" required>
            <!-- Display error message if it exists -->
            <?php
            if (isset($_SESSION['error'])) {
                echo "<p class='error-message'>" . $_SESSION['error'] . "</p>";
                unset($_SESSION['error']); // Clear the error message after displaying
            }
            ?>
            <button type="submit">Login</button>
        </form>
    </div>

</body>
</html>

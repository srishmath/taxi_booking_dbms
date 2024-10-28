<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Login</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h2>Customer Login</h2>
        <form action="login_action.php" method="POST">
            <label for="Number">Phone Number:</label>
            <input type="text" id="Number" name="Number" required>
            <label for="Password">Password:</label>
            <input type="password" id="Password" name="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>

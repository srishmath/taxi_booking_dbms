<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $c_id = $_POST['c_id'];
    $d_id = $_POST['d_id'];
} else {
    echo "<p>Invalid request.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Provide Feedback</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Provide Feedback</h1>
        <form action="submit_feedback.php" method="post">
            <input type="hidden" name="c_id" value="<?php echo htmlspecialchars($c_id); ?>">
            <input type="hidden" name="d_id" value="<?php echo htmlspecialchars($d_id); ?>">
            <label for="rating">Rating (1-5):</label>
            <input type="number" id="rating" name="rating" min="1" max="5" required><br>
            <label for="comments">Comments:</label><br>
            <textarea id="comments" name="comments" rows="4" cols="50"></textarea><br>
            <button type="submit">Submit Feedback</button>
        </form>
    </div>
</body>
</html>

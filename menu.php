<?php
session_start();

// Set up the database connection
$conn = new mysqli("localhost", "root", "", "hostel");

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Handle the voting submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['vote_option'])) {
    $vote = intval($_POST['vote_option']);
    if ($vote >= 1 && $vote <= 3) {
        $column = "votes_option" . $vote;
        $sql = "UPDATE menu SET $column = $column + 1 WHERE id = (SELECT MAX(id) FROM menu)";
        if ($conn->query($sql) === TRUE) {
            echo "<script>alert('Thank you for voting!');</script>";
        } else {
            echo "<script>alert('Error while voting. Please try again later.');</script>";
        }
    }
}

// Fetch the latest menu
$sql = "SELECT option1, option2, option3, votes_option1, votes_option2, votes_option3 FROM menu ORDER BY created_at DESC LIMIT 1";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Today's Menu</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            text-align: center;
        }
        h2 {
            margin: 20px 0;
            color: #333;
        }
        form {
            margin: 20px auto;
            display: inline-block;
            text-align: left;
        }
        ul {
            list-style-type: none;
            padding: 0;
        }
        li {
            margin: 10px 0;
        }
        button {
            padding: 5px 10px;
            cursor: pointer;
            background-color: #0071c2;
            color: white;
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #005fa3;
        }
    </style>
</head>
<body>

    <h2>Today's Menu</h2>

    <?php
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        ?>
        <form method="POST" action="">
            <ul>
                <li><?php echo $row['option1']; ?> - <button type="submit" name="vote_option" value="1">Vote</button> (Votes: <?php echo $row['votes_option1']; ?>)</li>
                <li><?php echo $row['option2']; ?> - <button type="submit" name="vote_option" value="2">Vote</button> (Votes: <?php echo $row['votes_option2']; ?>)</li>
                <li><?php echo $row['option3']; ?> - <button type="submit" name="vote_option" value="3">Vote</button> (Votes: <?php echo $row['votes_option3']; ?>)</li>
            </ul>
        </form>
        <?php
    } else {
        echo "<p>No menu has been set for today.</p>";
    }
    ?>

</body>
</html>

<?php
// Close the database connection
$conn->close();
?>

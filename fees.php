<?php
session_start();

// Database connection (update with your DB credentials)
$servername = "localhost";
$db_username = "root"; // Change to your DB username
$db_password = ""; // Change to your DB password
$dbname = "hostel"; // Change to your DB name

$conn = new mysqli($servername, $db_username, $db_password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// User Login Process
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query to check if the user exists in the `fees` table
    $sql = "SELECT * FROM fees WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Successful login
        $_SESSION['user_logged_in'] = true;
        $_SESSION['username'] = $username;
        header("Location: fees.php"); // Redirect to the same page to prevent re-submission
        exit();
    } else {
        $login_error = "Invalid username or password.";
    }
}

// User Logout Process
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: fees.php");
    exit();
}

// Retrieve Fees Information for Logged-in User
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    $username = $_SESSION['username'];
    $sql = "SELECT * FROM fees WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $fee_details = $result->fetch_assoc();
    } else {
        $error = "No fee details found for the user.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Management System - Fees</title>
    <style>
        body, html {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
        }
        input[type="text"], input[type="password"] {
            padding: 12px;
            margin: 8px 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            transition: border 0.3s ease;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border: 1px solid #0071c2;
        }
        button {
            padding: 12px;
            background: #0071c2;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            transition: background 0.3s ease;
        }
        button:hover {
            background: #005fa3;
        }
        .logout-link {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #0071c2;
            text-decoration: none;
        }
        .logout-link:hover {
            color: #005fa3;
            text-decoration: underline;
        }
        .error, .success {
            padding: 10px;
            margin-top: 20px;
            border-radius: 4px;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
        }
        .success {
            background: #d4edda;
            color: #155724;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
    </style>
</head>
<body>
<div class="container">
    <?php if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true): ?>
        <h2>Welcome, <?php echo htmlspecialchars($username); ?>! Here are your fee details:</h2>
        <?php if (isset($fee_details)): ?>
            <table>
                <tr>
                    <th>Total Fees</th>
                    <th>Fees Paid</th>
                    <th>Fees Due</th>
                </tr>
                <tr>
                    <td><?php echo $fee_details['total_fees']; ?></td>
                    <td><?php echo $fee_details['fees_paid']; ?></td>
                    <td><?php echo $fee_details['fees_due']; ?></td>
                </tr>
            </table>
        <?php else: ?>
            <p class="error">No fee details found.</p>
        <?php endif; ?>
        <a href="?logout=true" class="logout-link">Logout</a>
    <?php else: ?>
        <h2>User Login</h2>
        <form method="POST">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
            <?php if (isset($login_error)) { echo '<p class="error">'.$login_error.'</p>'; } ?>
        </form>
    <?php endif; ?>
</div>
</body>
</html>

<?php
$conn->close();
?>

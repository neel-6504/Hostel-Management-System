<?php
session_start();

// Database connection (update with your DB credentials)
$servername = "localhost";
$username = "root"; // Change to your DB username
$password = ""; // Change to your DB password
$dbname = "hostel"; // Change to your DB name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin Login Process
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Hardcoded admin credentials
    if ($username === 'admin' && $password === 'admin') {
        $_SESSION['admin_logged_in'] = true;
        header("Location: ?page=admin");
        exit();
    } else {
        $login_error = "Invalid username or password.";
    }
}

// Admin Logout Process
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ?");
    exit();
}

// Determine which page to show
$page = isset($_GET['page']) ? $_GET['page'] : 'login';

// If the admin is not logged in and tries to access the admin page, redirect to login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    $page = 'login';
}

// Admin Menu Update Process
if (isset($_POST['update_menu']) && isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    $menu_option_1 = $_POST['menu_option_1'];
    $menu_option_2 = $_POST['menu_option_2'];
    $menu_option_3 = $_POST['menu_option_3'];

    // Check if a menu already exists for today
    $check_sql = "SELECT id FROM menu WHERE DATE(created_at) = CURDATE()";
    $check_result = $conn->query($check_sql);

    if ($check_result->num_rows > 0) {
        // Update existing menu
        $menu_id = $check_result->fetch_assoc()['id'];
        $sql = "UPDATE menu SET option1='$menu_option_1', option2='$menu_option_2', option3='$menu_option_3', votes_option1=0, votes_option2=0, votes_option3=0 WHERE id=$menu_id";
    } else {
        // Insert new menu
        $sql = "INSERT INTO menu (option1, option2, option3, votes_option1, votes_option2, votes_option3) 
                VALUES ('$menu_option_1', '$menu_option_2', '$menu_option_3', 0, 0, 0)";
    }

    if ($conn->query($sql) === TRUE) {
        $success = "Today's menu has been updated successfully!";
    } else {
        $error = "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hostel Management System - Admin</title>
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
        .logout-link, .admin-link {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #0071c2;
            text-decoration: none;
        }
        .logout-link:hover, .admin-link:hover {
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
    </style>
</head>
<body>
<div class="container">
    <?php if ($page === 'admin' && isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
        <h2>Admin Dashboard</h2>
        <form method="POST">
            <input type="text" name="menu_option_1" placeholder="Menu Option 1" required>
            <input type="text" name="menu_option_2" placeholder="Menu Option 2" required>
            <input type="text" name="menu_option_3" placeholder="Menu Option 3" required>
            <button type="submit" name="update_menu">Update Menu</button>
        </form>
        <?php if (isset($success)) { echo '<p class="success">'.$success.'</p>'; } ?>
        <?php if (isset($error)) { echo '<p class="error">'.$error.'</p>'; } ?>
        <a href="?logout=true" class="logout-link">Logout</a>
    <?php elseif ($page === 'menu'): ?>
        <h2>Today's Menu</h2>
        <?php
        $sql = "SELECT option1, option2, option3 FROM menu ORDER BY created_at DESC LIMIT 1";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            ?>
            <ul>
                <li><span>Option 1:</span> <?php echo $row['option1']; ?></li>
                <li><span>Option 2:</span> <?php echo $row['option2']; ?></li>
                <li><span>Option 3:</span> <?php echo $row['option3']; ?></li>
            </ul>
        <?php } else { ?>
            <p>No menu has been set for today.</p>
        <?php } ?>
        <a href="?page=admin" class="admin-link">Admin Login</a>
    <?php else: ?>
        <h2>Admin Login</h2>
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

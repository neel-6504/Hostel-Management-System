<?php
session_start(); // Start the session

$server = "localhost";
$username = "root";
$password = "";
$dbname = "hostel";
$error = "";

$con = mysqli_connect($server, $username, $password, $dbname);

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $name = mysqli_real_escape_string($con, $_POST['username']);
        $pass = mysqli_real_escape_string($con, $_POST['password']);

        $sql = "SELECT * FROM users WHERE username = '$name'";
        $result = mysqli_query($con, $sql);

        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            if ($row['password'] === $pass) {
                $_SESSION['username'] = $name;
                $_SESSION['login_time'] = time();
                $_SESSION['last_activity'] = time();
                echo "<script>alert('Login successful. Redirecting...');</script>";
                header("Location: index.php");
                exit();
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Divine Hostel</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Roboto', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #74ebd5, #acb6e5);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            padding: 20px;
        }

        .login-container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.3);
            width: 400px;
            padding: 30px;
            text-align: center;
        }

        .login-container h2 {
            color: #555;
            font-size: 28px;
            margin-bottom: 20px;
            font-weight: 700;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
        }

        .login-container input {
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 25px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .login-container input:focus {
            border-color: #7b9acc;
            outline: none;
            box-shadow: 0 0 5px rgba(123, 154, 204, 0.5);
        }

        .login-container input[type="submit"] {
            background-color: #7b9acc;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
            padding: 12px;
        }

        .login-container input[type="submit"]:hover {
            background-color: #5a7bbf;
        }

        .error {
            color: red;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
<?php
    $server = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hostel";
    $error = "";

    $con = mysqli_connect($server, $username, $password, $dbname);

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            $name = mysqli_real_escape_string($con, $_POST['username']);
            $pass = mysqli_real_escape_string($con, $_POST['password']);

            $sql = "SELECT * FROM users WHERE username = '$name'";
            $result = mysqli_query($con, $sql);

            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                if ($row['password'] === $pass) {
                    $_SESSION['username'] = $name;
                    $_SESSION['login_time'] = time();
                    echo "<script>alert('Login successful. Redirecting...');</script>";
                    header("Location: index.php");
                    exit();
                } else {
                    $error = "Invalid password.";
                }
            } else {
                $error = "User not found.";
            }
        } else {
            $error = "Please fill in all fields.";
        }
    }

    mysqli_close($con);
    ?>

    <div class="login-container">
        <h2>Login to Divine Hostel</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post" action="">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>
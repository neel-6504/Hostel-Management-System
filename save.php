    <?php
    $server = "localhost";
    $username = "root";
    $password = "";
    $dbname = "hostel";
    $error = "";

    session_start();

    if (isset($_GET['action']) && $_GET['action'] == 'logout') {
        if (isset($_SESSION['username'])) {
            $logout_time = time();
            $login_time = $_SESSION['login_time'];
            $session_duration = $logout_time - $login_time;
            echo "Session duration: " . gmdate("H:i:s", $session_duration);

            session_unset();
            session_destroy();
            header("Location: login.php");
            exit();
        }
    }


    // Create connection
    $con = mysqli_connect($server, $username, $password, $dbname);

    // Check connection
    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Check if the form data is set
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['username']) && isset($_POST['password'])) {
            // Get the data from the form
            $name = mysqli_real_escape_string($con, $_POST['username']);
            $pass = mysqli_real_escape_string($con, $_POST['password']);

            // SQL query to fetch the user data by username
            $sql = "SELECT * FROM users WHERE username = '$name'";
            $result = mysqli_query($con, $sql);

            // Check if a user with the given username exists
            if (mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                
                // Verify the password
                if ($row['password'] === $pass) {
                    echo "<script>alert('Login successful. Redirecting...');</script>";
                    // Redirect to index.html after successful login
                    header("Location: index.html");
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

    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();

    // Close the connection
    mysqli_close($con);
    ?>
    
<?php
session_start();

if (isset($_SESSION['username'])) {
    $logout_time = time();
    $login_time = $_SESSION['login_time'];
    $session_duration = $logout_time - $login_time;
    echo "Session duration: " . gmdate("H:i:s", $session_duration);

    // Save the session duration to the database if needed

    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
} else {
    header("Location: login.php");
    exit();
}
?>

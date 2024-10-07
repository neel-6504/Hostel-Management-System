<?php
session_start();

// Set the session timeout duration in seconds (5 minutes)
$timeout_duration = 5 * 60; // 5 minutes

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Check if the session has expired
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // Session has expired
    session_unset();
    session_destroy();
    header("Location: login.php");
    exit();
}

// Initialize the page variable
$page = isset($_GET['page']) ? $_GET['page'] : 'home'; // Default to 'home' if not set

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

// Display Menu if the page is 'menu'
if ($page === 'menu') {
    echo '<h2>Today\'s Menu</h2>';

    $sql = "SELECT option1, option2, option3, votes_option1, votes_option2, votes_option3 FROM menu ORDER BY created_at DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        ?>
        <form method="POST">
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
}

// Update last activity time
$_SESSION['last_activity'] = time();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Page</title>
    <style>
        /* Global Styles */
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Header Styles */
        header {
            background-color: rgba(0, 42, 92, 0.95);
            padding: 10px 20px;
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1200px;
            margin: auto;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: white;
        }

        .nav-links {
            list-style: none;
            display: flex;
            gap: 20px;
        }

        .nav-links li {
            cursor: pointer;
            color: white;
            font-size: 16px;
        }

        .nav-links a {
            text-decoration: none;
            color: white;
        }

        .nav-links a:hover {
            text-decoration: underline;
        }

        .session-info {
            color: white;
            font-size: 14px;
        }

        .auth-buttons {
            display: flex;
            gap: 10px;
        }

        .auth-buttons a {
            padding: 5px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease, transform 0.3s ease;
            text-decoration: none;
            color: white;
        }

        .auth-buttons a.logout-button {
            background-color: #e74c3c;
        }

        .auth-buttons a.logout-button:hover {
            background-color: #c0392b;
            transform: scale(1.05); /* Slightly enlarge button */
        }

        /* Smooth scroll for anchors */
        html {
            scroll-behavior: smooth;
        }

        /* Background Image Styles */
        .bg {
            position: relative;
            background-image: url('main.png');
            background-size: cover;
            background-repeat: no-repeat;
            background-position: center center;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .bg::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5); /* Black overlay with 50% opacity */
            z-index: 1;
        }

        /* Main Text Styling */
        .main-text {
            position: relative;
            z-index: 2;
            text-align: center;
            color: #fff;
        }

        .main-text h1 {
            font-size: 3em;
            font-family: 'Georgia', serif;
            margin-bottom: 10px;
            color: #67b6ff;
            margin-top: -20px; 
            margin-bottom: 30px;
        }

        .main-text h6 {
            font-size: 1.5em;
            font-family: 'Arial', sans-serif;
            font-style: oblique;
            font-weight: bold;
            color: #8698a4;
            margin-bottom: 40px;
        }

        /* Card Container Styling */
        .cards-container {
            margin-top: 20px;
            display: flex;
            flex-wrap: wrap;
            gap: 70px;
            justify-content: center;
            padding: 20px;
            z-index: 2; 
        }

        /* Card Styling */
        .card {
            display: flex;
            flex-direction: column;
            width: 250px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
            text-decoration: none;
            color: #333;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-image {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .card h3 {
            font-size: 1.5em;
            margin: 10px;
        }

        .card p {
            font-size: 1em;
            margin: 0 10px 10px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .card:hover, .card:visited {
            color: #333;
        }

        /* Room Section Styling */
        .room-section {
            margin-top: 10px;
            padding: 40px;
            text-align: center;
            background-color: #f9f9f9;
            box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px; 
            border: none; 
            border-radius: 8px; 
            max-width: 1200px; 
            margin: auto; 
            margin-top: 20px;
            margin-bottom: 20px;
        }

        /* Room Styling */
        .room {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
            max-width: 1200px;
        }

        .room-image {
            width: 50%;
            height: auto;
            object-fit: cover;
        }

        .room-content {
            padding: 20px;
            text-align: left;
            /* display: flex;
            flex-direction: column;
            justify-content: center; */
        }

        .room h3 {
            font-size: 2em;
            margin-bottom: 10px;
        }

        .room p {
            font-size: 1em;
            margin-bottom: 20px;
        }

        .features {
            list-style-type: none;
            padding: 0;
        }

        .features li {
            font-size: 1em;
            margin-bottom: 10px;
            color: #0071c2;
            font-weight: bold;
        }

        /* Inquiry and Complaint Sections */
        .inquiry-section, .complaint-section {
            padding: 40px;
            text-align: center;
            background-color: #f9f9f9;
        }

        /* Footer Styles */
        .footer {
            background-color: #f1f1f1;
            padding: 40px 20px;
            color: #333;
            text-align: center;
        }

        .footer-content {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
            align-items: flex-start;
            max-width: 1200px;
            margin: auto;
            gap: 20px;
        }

        .inquiry-form, .contact-info {
            max-width: 500px;
            flex: 1;
            margin: 10px;
            min-width: 250px;
        }

        .inquiry-form h3, .contact-info h3 {
            font-size: 1.8em;
            margin-bottom: 20px;
        }

        .inquiry-form form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .inquiry-form input, .inquiry-form textarea {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        .inquiry-form button {
            background-color: #0071c2;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        .inquiry-form button:hover {
            background-color: #005fa3;
        }

        .contact-info p {
            font-size: 1em;
            margin: 10px 0;
        }

        .contact-info a {
            color: #0071c2;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        .footer-bottom {
            margin-top: 20px;
        }

        .footer-bottom p {
            font-size: 0.9em;
            color: #555;
        }
    </style>
</head>
<body>

        <header>
            <nav class="nav">
                <div class="logo">Divine Hostel</div>
                <ul class="nav-links">
                    <li><a href="#rooms">Rooms</a></li>
                    <li><a href="#footer">Inquiry</a></li>
                    <li><a href="#comp">Complaint</a></li>
                </ul>
                <div class="session-info">
                    Current Session Time: <span id="session-time"></span>
                </div>
                <div class="auth-buttons">
                    <a class="logout-button" href="logout.php">Logout</a>
                </div>
                
            </nav>
        </header>
    
        <div class="bg" style="background-image: url('main.png');">
            <div class="main-text">
                <h1>Your home far from home</h1>
                <h6>Check availability now!</h6>
            </div>
            <div class="cards-container">
                <a href="fees.php" class="card">
                    <img src="fee.jpg" alt="Fees" class="card-image">
                    <h3>Fees</h3>
                    <p>View the details about various fees.</p>
                </a>
                <a href="menu.php" class="card">
                    <img src="food.jpg" alt="Today's Menu" class="card-image">
                    <h3>Today's Menu</h3>
                    <p>Check out today’s menu options.</p>
                </a>
                <a href="complaint.html" class="card" id="comp">
                    <img src="comp.jpg" alt="Complaint" class="card-image">
                    <h3>Complaint</h3>
                    <p>Submit a complaint or feedback.</p>
                </a>
                <a href="leave.html" class="card">
                    <img src="leave.jpg" alt="Inquiry" class="card-image">
                    <h3>Leave</h3>
                    <p>Take a leave application from here</p>
                </a>
            </div>
        </div>
    
        <div class="room-section" id="rooms">
            <h2>Our Rooms</h2>
            <div class="room">
                <img src="360_F_219669327_v12pBKc7TB62E3uCJrgRRkDhfVENK3z5.webp" alt="Deluxe Room" class="room-image">
                <div class="room-content">
                    <p>Enjoy a spacious room with a queen-sized bed, en-suite bathroom, and a stunning city view. Perfect for travelers seeking comfort and luxury.</p>
                    <ul class="features">
                        <li>Free Wi-Fi</li>
                        <li>Room service</li>
                        <li>24*7 Swimming Pool</li>
                        <li>Air conditioning</li>
                        <li>Mess Facility</li>
                        <li>Huge Playground</li>
                    </ul>
                </div>
            </div>
        </div>
    
        <footer class="footer" id="footer">
            <div class="footer-content">
                <div class="inquiry-form">
                    <h3>More Inquiry</h3>
                    <form action="#" method="post">
                        <input type="text" name="name" placeholder="Your Name" required>
                        <input type="email" name="email" placeholder="Your Email" required>
                        <textarea name="message" placeholder="Your Message" required></textarea>
                        <button type="submit">Submit</button>
                    </form>
                </div>
                <div class="contact-info">
                    <h3>Contact Us</h3>
                    <p><strong>Address:</strong> 123 Hostel Lane, Changa, Gujarat</p>
                    <p><strong>Email:</strong> <a href="mailto:info@hostel.com">info@hostel.com</a></p>
                    <p><strong>Phone:</strong> +91 99999 99999</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Divine Hostel. All rights reserved.</p>
            </div>
        </footer>
    
        <script>
            // JavaScript to update session time
            function updateSessionTime() {
                const loginTime = <?php echo $_SESSION['login_time']; ?>;
                const currentTime = Math.floor(Date.now() / 1000);
                const elapsedTime = currentTime - loginTime;
    
                const hours = Math.floor(elapsedTime / 3600);
                const minutes = Math.floor((elapsedTime % 3600) / 60);
                const seconds = elapsedTime % 60;
    
                document.getElementById('session-time').textContent = 
                    String(hours).padStart(2, '0') + ':' +
                    String(minutes).padStart(2, '0') + ':' +
                    String(seconds).padStart(2, '0');
            }
    
            setInterval(updateSessionTime, 1000);
        </script>
    </body>
    </html>
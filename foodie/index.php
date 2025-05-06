<?php
session_start();
$servername = "localhost"; // Change if necessary
$username = "root"; // Your database username
$password = ""; // Your database password
$dbname = "canteen_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['login'])) {
        // Handle Login
        $login = $_POST['login'];
        $password = $_POST['password'];

        // Check if user exists in users table
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Redirect to index.php if valid
                $_SESSION['username'] = $login;
                header("Location: user_dashboard.php"); // Redirect to user dashboard
                exit();
            } else {
                echo "Invalid username or password.";
            }
        } else {
            echo "Invalid username or password.";
        }
        $stmt->close();

    } elseif (isset($_POST['signup'])) {
        // Handle Signup
        $signupLogin = $_POST['signupLogin'];
        $signupEmail = $_POST['signupEmail'];
        $signupPassword = password_hash($_POST['signupPassword'], PASSWORD_DEFAULT);

        // Insert new user into users table
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $signupLogin, $signupEmail, $signupPassword);

        if ($stmt->execute()) {
            echo "User registered successfully!";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } elseif (isset($_POST['admin_login'])) {
        // Handle Admin Login
        $adminLogin = $_POST['adminLogin'];
        $adminPassword = $_POST['adminPassword'];

        // Check if admin exists in admins table
        $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ? AND password = ?");
        $stmt->bind_param("ss", $adminLogin, $adminPassword); // Change this to use password_hash in production
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $_SESSION['admin'] = $adminLogin; // Save admin session
            header("Location: admin_dashboard.php"); // Change this to your admin dashboard page
            exit();
        } else {
            echo "Invalid admin credentials.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./css/login.css">
    <!-- Include Anime.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
</head>
<style>
          input[type=text], input[type=password], input[type=email] {
            background-color: #f6f6f6;
            border: none;
            color: #0d0d0d;
            padding: 15px 32px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 5px;
            width: 85%;
            border: 2px solid #f6f6f6;
            transition: all 0.5s ease-in-out;
            border-radius: 5px;
        }

        input[type=text]:focus, input[type=password]:focus, input[type=email]:focus {
            background-color: #fff;
            border-bottom: 2px solid #5fbae9;
        }
</style>
<body>
    <div class="wrapper fadeInDown">
        <div id="formContent">
          <!-- Tabs Titles -->
          <h2 class="active" onclick="showForm('login')"> Sign In </h2>
          <h2 class="inactive underlineHover" onclick="showForm('signup')">Sign Up </h2>
          <h2 class="inactive underlineHover" onclick="showForm('admin')">Admin </h2>

          
        <!-- Login Form -->
        <form id="loginForm" class="active" action="home.php" method="POST">
            <input type="text" name="login" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="submit" name="login" value="Log In">
        </form>
      
        <!-- Sign Up Form -->
        <form id="signupForm" action="index.php" method="POST" style="display: none;">
            <input type="text" name="signupLogin" placeholder="Username" required>
            <input type="email" name="signupEmail" placeholder="Email" required>
            <input type="password" name="signupPassword" placeholder="Password" required>
            <input type="submit" name="signup" value="Sign Up">
        </form>
      
        <!-- Admin Form -->
        <form id="adminForm" action="admin_dashboard.php" method="POST" style="display: none;">
            <input type="text" name="adminLogin" placeholder="Admin Login" required>
            <input type="password" name="adminPassword" placeholder="Admin Password" required>
            <input type="submit" name="admin_login" value="Log In as Admin">
        </form>
      
          <!-- Remind Password -->
          <div id="formFooter">
            <a class="underlineHover" href="#">Forgot Password?</a>
          </div>
      
        </div>
      </div>
      
      <script>
        function showForm(formType) {
            // Hide all forms
            document.getElementById('loginForm').style.display = 'none';
            document.getElementById('signupForm').style.display = 'none';
            document.getElementById('adminForm').style.display = 'none';

            // Remove active class from all tabs
            document.querySelectorAll('#formContent h2').forEach(tab => {
                tab.classList.remove('active');
                tab.classList.add('inactive');
            });

            // Show the selected form
            if (formType === 'login') {
                document.getElementById('loginForm').style.display = 'block';
                document.querySelector('h2.active:nth-of-type(1)').classList.add('active');
            } else if (formType === 'signup') {
                document.getElementById('signupForm').style.display = 'block';
                document.querySelector('h2.active:nth-of-type(2)').classList.add('active');
            } else if (formType === 'admin') {
                document.getElementById('adminForm').style.display = 'block';
                document.querySelector('h2.active:nth-of-type(3)').classList.add('active');
            }
        }
      </script>
</body>
</html>

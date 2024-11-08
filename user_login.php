<?php
// Enable error reporting (for debugging purposes)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Database connection settings
$db_host = 'localhost';
$db_username = 'root';
$db_password = '';
$db_name = 'aarthisoftware';

// Create connection
$conn = new mysqli($db_host, $db_username, $db_password, $db_name);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Function to sanitize user input to prevent SQL injection
function sanitizeInput($data)
{
  global $conn;
  return htmlspecialchars(mysqli_real_escape_string($conn, trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Sanitize and retrieve form data
  $username = sanitizeInput($_POST["username"]);
  $password = $_POST["password"];

  // Validate username and password format
  if (preg_match("/^[a-zA-Z0-9._%+\s-]+$/", $username) && preg_match("/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/", $password)) {
    // Check if username exists in the database
    $sql = "SELECT * FROM signup WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
      // Fetch the user details
      $user = $result->fetch_assoc();

      // Assuming password is stored in plain text (not recommended)
      $stored_password = $user['password'];

      // Verify the password
      if ($password === $stored_password) {
        // Passwords match, login successful
        session_start();
        $_SESSION['username'] = $username;
        echo "<script>alert('Login successfully!');</script>";
        header("Location: User/animation.php"); // Redirect to main page
        exit();
      } else {
        // Passwords do not match
        echo "<script>alert('Invalid credentials. Please check your username and password!');</script>";
      }
    } else {
      // User not found
      echo "<script>alert('Invalid credentials. Please check your username and password!');</script>";
    }

    $stmt->close();
  } else {
    echo "<script>alert('Please fill out the form correctly!');</script>";
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <style>
    /* Base styles */
    * {
          margin: 0;
          padding: 0;
          box-sizing: border-box;
          font-family: 'Poppins', sans-serif;
        }
        
        body {
          display: flex;
          justify-content: center;
          align-items: center;
          height: 100vh;
          background: #ff8c00;
        }
        
        .container {
          width: 400px;
          background: #fff;
          padding: 30px;
          border-radius: 8px;
          box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .title {
      font-size: 24px;
      font-weight: 600;
      margin-bottom: 25px;
      text-align: center;
    }
        
        .input-boxes {
          display: flex;
          flex-direction: column;
        }
        
        .input-box {
          display: flex;
          align-items: center;
          margin-bottom: 15px;
          position: relative; /* Add this line */
        }
        .eye-icon {
          cursor: pointer;
          position: absolute;
          right: 15px; /* Adjusted right spacing */
          top: 50%; /* Center vertically */
          transform: translateY(-40%); /* Center the icon vertically */
          color: #555;
        }        
        .input-box i {
          font-size: 18px;
          margin-right: 10px;
          color: #555;
        }
        
        .input-box input {
          width: 100%;
          padding: 10px;
          border: 1px solid #ddd;
          border-radius: 5px;
          outline: none;
        }
        
        .input-box input:focus {
          border-color: #ff8c00;
        }
        
        .button {
          display: flex;
          justify-content: center;
          margin-top: 20px;
        }
        
        .button button {
          background: #ff8c00; /* Orange background */
          color: #fff;         /* White text */
          border: none;        /* No border */
          padding: 10px 20px;  /* Padding */
          border-radius: 5px;  /* Rounded corners */
          cursor: pointer;      /* Pointer cursor on hover */
          transition: background 0.3s ease; /* Smooth background color transition */
        }
        
        .button button:hover {
          background: #e07b00; /* Darker orange on hover */
        }

        .error {
          color: red;
          font-size: 14px;
          text-align: center;
          margin-top: 10px;
        }

        .signuplink {
          text-align: center;
          margin-top: 15px;
        }
        
        .signuplink a {
          color: #ff8c00;
          text-decoration: none;
        }
        
        .signuplink a:hover {
          text-decoration: underline;
        }

    /* Responsive styles */
    @media (max-width: 480px) {
      .container {
        padding: 20px;
        max-width: 90%;
      }

      .title {
        font-size: 20px;
      }

      .input-box i {
        font-size: 16px;
      }

      .button button {
        padding: 12px;
      }
    }

    @media (min-width: 768px) {
      .container {
        max-width: 600px;
      }

      .title {
        font-size: 26px;
      }
    }
  </style>
</head>

<body>
<div class="container">
    <div class="forms">
      <div class="form-content">
        <div class="login-form">
          <div class="title">Login</div>
          <form action="user_login.php" method="POST"> <!-- Updated path -->
            <div class="input-boxes">
              <div class="input-box">
                <i class="fas fa-user"></i>
                <input type="text" id="username" name="username" placeholder="Enter your Username" required>
              </div>
              <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="fas fa-eye eye-icon" id="togglePassword" onclick="togglePassword()"></i>
              </div>
              <div class="button input-box">
                <button type="submit">Submit</button>
              </div>
              <div class="signuplink">
                Don't have an account? <a href="user_signup.php">Sign Up</a><br>
                <a href="fetch_password.php">Forgot Password?</a>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <script>
    function togglePassword() {
        const passwordField = document.getElementById('password');
        const toggleIcon = document.getElementById('togglePassword');
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;
        toggleIcon.classList.toggle('fa-eye');
        toggleIcon.classList.toggle('fa-eye-slash');
    }

    function toggleConfirmPassword() {
        const confirmPasswordField = document.getElementById('confirmPassword');
        const toggleIcon = document.getElementById('toggleConfirmPassword');
        const type = confirmPasswordField.type === 'password' ? 'text' : 'password';
        confirmPasswordField.type = type;
        toggleIcon.classList.toggle('fa-eye');
        toggleIcon.classList.toggle('fa-eye-slash');
    }
</script>
</body>

</html>

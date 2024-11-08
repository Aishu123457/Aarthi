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

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);
    echo "<script>alert('Connected successfully');</script>";
} catch (mysqli_sql_exception $e) {
    echo "<script>alert('Connection failed: " . $e->getMessage() . "');</script>";
    die();
}
// Function to sanitize input
function sanitizeInput($data)
{
    return htmlspecialchars(stripslashes(trim($data)));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = sanitizeInput($_POST['username']);
    $country_code = sanitizeInput($_POST['country_code']);
    $phone_number = sanitizeInput($_POST['phone_number']);
    $address = sanitizeInput($_POST['address']);
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $confirmPassword = sanitizeInput($_POST['confirmPassword']);

    // Updated regex to allow spaces and hyphens in username
    if (!preg_match('/^[a-zA-Z0-9._%+\s-]+$/', $username)) {
        echo "<script>alert('Invalid username format.');</script>";
    } elseif (!preg_match('/^[0-9]{10}$/', $phone_number)) {
        echo "<script>alert('Mobile number must be exactly 10 digits.');</script>";
    } elseif (!preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        echo "<script>alert('Password must be at least 8 characters long and contain both letters and numbers.');</script>";
    } elseif ($password !== $confirmPassword) {
        echo "<script>alert('Passwords do not match.');</script>";
    } else {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM signup WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo "<script>alert('Username already exists.');</script>";
        } else {
            $stmt->close();

            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM signup WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                echo "<script>alert('Email already exists.');</script>";
            } else {
                $stmt->close();

                // Prepare SQL statement to prevent SQL injection
                $stmt = $conn->prepare("INSERT INTO signup (username, country_code, phone_number, address, email, password) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssss", $username, $country_code, $phone_number, $address, $email, $password);

                if ($stmt->execute()) {
                    echo "<script>alert('Sign up successful!');</script>";
                    // Redirect to login page or another page
                    header("Location: user_login.php");
                    exit();
                } else {
                    echo "<script>alert('Error: " . $stmt->error . "');</script>";
                }

                $stmt->close();
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
    <style>
        /* Existing CSS styles */
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
            position: relative;
            /* Add this line */
        }

        .input-box i {
            font-size: 18px;
            margin-right: 10px;
            color: #555;
        }

        .eye-icon {
            cursor: pointer;
            position: absolute;
            right: 15px;
            /* Adjusted right spacing */
            top: 50%;
            /* Center vertically */
            transform: translateY(-10%);
            /* Center the icon vertically */
            color: #555;
        }

        .input-box input,
        .input-box select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }

        .input-box input:focus,
        .input-box select:focus {
            border-color: #ff8c00;
        }

        .button {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }

        .button button {
            background: #ff8c00;
            /* Orange background */
            color: #fff;
            /* White text */
            border: none;
            /* No border */
            padding: 10px 20px;
            /* Padding */
            border-radius: 5px;
            /* Rounded corners */
            cursor: pointer;
            /* Pointer cursor on hover */
            transition: background 0.3s ease;
            /* Smooth background color transition */
        }

        .button button:hover {
            background: #e07b00;
            /* Darker orange on hover */
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

        .eye-icon {
            cursor: pointer;
            position: absolute;
            right: 10px;
            top: 10px;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="container">
        <form id="signupForm" method="POST" onsubmit="return validateForm();">
            <div class="title">Sign Up</div>
            <div class="input-box">
                <i class="fas fa-user"></i>
                <input type="text" id="username" name="username" placeholder="Username" required>
            </div>
            <div class="input-box">
                <i class="fas fa-globe"></i>
                <select id="country_code" name="country_code" required>
                    <option disabled selected>Select country code</option>
                    <option value="+91">+91 (India)</option>
                    <option value="+1">+1 (United States)</option>
                    <option value="+44">+44 (United Kingdom)</option>
                    <option value="+61">+61 (Australia)</option>
                    <option value="+81">+81 (Japan)</option>
                    <option value="+86">+86 (China)</option>
                    <option value="+49">+49 (Germany)</option>
                    <option value="+33">+33 (France)</option>
                    <option value="+39">+39 (Italy)</option>
                    <option value="+7">+7 (Russia)</option>
                    <option value="+55">+55 (Brazil)</option>
                    <option value="+34">+34 (Spain)</option>
                    <option value="+27">+27 (South Africa)</option>
                    <option value="+52">+52 (Mexico)</option>
                    <option value="+47">+47 (Norway)</option>
                    <option value="+46">+46 (Sweden)</option>
                    <option value="+65">+65 (Singapore)</option>
                    <option value="+31">+31 (Netherlands)</option>
                    <option value="+64">+64 (New Zealand)</option>
                    <option value="+971">+971 (United Arab Emirates)</option>

                </select>
            </div>
            <div class="input-box">
                <i class="fas fa-phone"></i>
                <input type="text" id="phone_number" name="phone_number" placeholder="Phone Number (10 digits)"
                    pattern="\d{10}" maxlength="10" required>
            </div>

            <div class="input-box">
                <i class="fas fa-home"></i>
                <input type="text" id="address" name="address" placeholder="Address" required>
            </div>
            <div class="input-box">
                <i class="fas fa-envelope"></i>
                <input type="email" id="email" name="email" placeholder="Email ID" required>
            </div>
            <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password" id="password" name="password" placeholder="Password" required>
                <i class="fas fa-eye eye-icon" id="togglePassword" onclick="togglePassword()"></i>
            </div>
            <div class="input-box">
                <i class="fas fa-lock"></i>
                <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm Password" required>
                <i class="fas fa-eye eye-icon" id="toggleConfirmPassword" onclick="toggleConfirmPassword()"></i>
            </div>
            <div class="button">
                <button type="submit">Sign Up</button>
            </div>
            <div class="signuplink">
                <p>Already have an account? <a href="user_login.php">Login here</a></p>
            </div>
        </form>
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
    <script>
        function validateForm() {
            var password = document.getElementById("password").value;
            var confirmPassword = document.getElementById("confirmPassword").value;

            if (password !== confirmPassword) {
                alert("Passwords do not match.");
                return false;
            }
            return true;
        }
    </script>
    <script>
        document.getElementById('phone_number').addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove non-digits
            if (value.length > 10) {
                value = value.slice(0, 10); // Limit to 10 digits
            }
            e.target.value = value;
        });
    </script>
</body>

</html>
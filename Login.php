<?php
session_start();
require_once 'db_connect.php'; // Ensure this file path is correct
global $conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uname = $_POST['uname']; // Username input
    $password = $_POST['password'];
    
    if (empty($uname) || empty($password)) {
        echo "<script>alert('Please enter Username and Password.'); window.location.href='Login.php';</script>";
        exit();
    }
    
    $sql = "SELECT * FROM loginuserlevels WHERE UNAME = ? OR Name = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ss", $uname, $uname); // Binding both the `uname` for `UNAME` and `Name`
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Check if the password matches (assuming the password is stored in plain text)
            if ($user['PASSWORD'] === $password) {
                $_SESSION['username'] = $user['UNAME']; // You can store `UNAME` or `Name` in the session
                
                // Redirect based on role
                $redirects = [
                    "0" => "Users/REGISTAR/REGISTAR.php",
                    "1" => "Users/CITCS/DEAN/DEAN.php",
                    "2" => "Users/CITCS/ACT/ACT.php",
                    "3" => "Users/CITCS/IT/IT.php",
                    "4" => "Users/CITCS/CS/CS.php",
                    "5" => "Users/CTE/DEAN/DEANCTE.php",
                    "6" => "Users/CTE/PH/PH.php"
                ];
                
                header("Location: " . ($redirects[$user['UCODE']] ?? "default_dashboard.php"));
                exit();
            } else {
                echo "<script>alert('Incorrect password.'); window.location.href='Login.php';</script>";
            }
        } else {
            echo "<script>alert('Invalid credentials.'); window.location.href='Login.php';</script>";
        }
        $stmt->close();
    } else {
        die("Database error: " . $conn->error);
    }
    
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
       <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <title>University Login</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: url('background.png') no-repeat center center fixed;
            background-size: cover;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        body.loaded {
            opacity: 1;
        }

.login-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    background: rgba(255, 0, 0, 0.3);
    padding: 60px;
    border-radius: 15px;
    box-shadow: 0px 0px 15px rgba(255, 0, 0, 0.2);
    width: 450px;
    text-align: center;
    backdrop-filter: blur(10px);
    transform: scale(0.8);
    opacity: 0;
    transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
    min-height: 650px; /* Increased height */
    position: relative;
    margin-bottom: 100px; /* Extra space at the bottom */
}

.marquee-container {
    width: 100%;
    position: absolute;
    bottom: 10px; /* Make sure it sticks to the bottom */
    left: 0;
    text-align: center;
    padding: 5px 0;
    background: rgba(0, 0, 0, 0.2); /* Slight background to separate it */
    border-radius: 0 0 15px 15px; /* Match the container shape */
}

/* Ensure text is visible */
.marquee-container marquee {
    font-size: 14px;
    color: white;
    font-weight: bold;
    display: block;
    width: 100%;
}

        h2 {
            font-size: 36px;
            margin-bottom: 25px;
            color: white;
        }

        .input-group {
           font-size: 20px; /* Make text bigger inside inputs */
            display: flex;
            align-items: center;
            width: 100%;
            border: 2px solid white;
            border-radius: 5px;
            padding: 10px;
            margin: 10px 0;
        }

        .input-group .icon {
            font-size: 25px;
            color: white;
            margin-right: 10px;
        }

        .input-group input {
    width: 100%;
    background: transparent;
    border: none;
    outline: none;
      color: #FFFDD0; /* Cream color */
    padding: 15px;
    font-size: 18px;
}
::placeholder {
    color: rgba(255, 253, 208, 0.8); /* Cream-like placeholder */
    font-weight: bold;
}



        body.loaded .login-container {
            transform: scale(1);
            opacity: 1;
        }
button, .signup-button {
    font-size: 20px; /* Bigger buttons */
    padding: 15px;
}
       button {
    width: 100%;
    padding: 12px;
    background:transparent;
    color: white;
    border: 2px solid white;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    border-radius: 5px;
    margin-top: 15px;
    transition: background 0.3s ease-in-out, transform 0.2s;
}

.signup-button {
    display: block;  /* Make it appear as a block element */
    width: 100%;
    background: transparent;
    color: white;
    border: 2px solid white;
    font-weight: bold;
    text-align: center;
    text-decoration: none;
    border-radius: 10px;
    margin-top: 15px; /* Space between Login and Sign Up */
    transition: background 0.3s ease-in-out, transform 0.2s;
}

.signup-button:hover {
    background: green;
    color: white;
    transform: scale(1.05);
}


        button:hover {
              background: green;
    color: white;
            transform: scale(1.05);
        }

        .toggle-password {
            cursor: pointer;
            color: white;
        }
        .forgot-password-container {
    text-align: right;
    margin-top: 30px;
}

.forgot-password {
    font-size: 18px; /* Make it small */
    color: white;
    text-decoration: none;
}

.forgot-password:hover {
    text-decoration: underline;
}         
        .logo {
    width: 100px; /* Adjust size as needed */
    display: block;
    margin: 0 auto 15px; /* Center the logo and add space below */
    filter: invert(1);
}
     label {
    color: white;
    font-weight: bold;
     font-size: 25px;
      text-align: left; /* Align text to the left */
    display: block; /* Ensure it takes full width */
}   
footer {
    position: fixed;
    bottom: 0;
    left: 0;
    width: 100%;
    background: rgba(0, 77, 0, 0.6); /* Dark green with 60% transparency */
    color: white;
    text-align: center;
    padding: 10px;
    font-size: 14px;
    font-weight: bold;
    backdrop-filter: blur(5px); /* Blurred background effect */
    border-top: 1px solid rgba(255, 255, 255, 0.3); /* Soft border to improve readability */
}

/* Styling links in the footer */
footer a {
    color: white;
    text-decoration: none;
}

footer a:hover {
    text-decoration: underline;
}



    </style>
</head>

<body onload="document.body.classList.add('loaded')">

    <div class="login-container">
        <img src="alumni.png" alt="Cunanan Academy Logo" class="logo">
        <h2>University Login</h2>
        <form method="POST" action="login.php">
            <label for="uname">Username:</label>
            <div class="input-group">
                <span class="icon">👤</span>
                <input type="text" id="uname" name="uname" placeholder="Enter your Username" required>
            </div>

            <label for="password">Password:</label>
            <div class="input-group">
                <span class="icon">🔒</span>
                <input type="password" id="password" name="password" placeholder="Enter your Password" required>
                <span class="toggle-password" onclick="togglePassword()">👁️</span>
            </div>

     <button type="submit">Login</button>
<a href="http://localhost/AlumniSystem/Users/register.php" class="signup-button">Sign Up</a>


<div class="forgot-password-container">
    <a href="forgot_password.php" class="forgot-password">Forgot Password?</a>
</div>
    
        </form>
         <div class="marquee-container">
        <marquee behavior="scroll" direction="left" scrollamount="5">
            📢 Welcome to the University Login Portal! Please enter your Username and Password to proceed. If you experience any issues, contact IT support. 🚀 Stay updated with the latest announcements, exam schedules, and academic news. 📅 Check your student portal for important notifications. 💡 For password recovery or login issues, reach out to our IT helpdesk. 🔧 Have a great day and happy learning! 🎓📚
        </marquee>
    </div>
</div>

       <footer>
        <p>
            <a href="https://www.google.com/maps/search/?api=1&query=University+Road+NBP+Reservation+Brgy.+Poblacion%2C+City+of+Muntinlupa%2C+Philippines%2C+1776" target="_blank">
                <i class="fas fa-map-marker-alt fa-sm"></i> University Road NBP Reservation Brgy. Poblacion, City of Muntinlupa. Philippines, 1776
            </a>
        </p>
        <p><i class="far fa-envelope fa-sm"></i> plmuncomm@plmun.edu.ph</p>
        <p>
            <a href="https://www.facebook.com/PLMUN.BSCS.SOCIETY" target="_blank">
                <i class="fab fa-facebook-square fa-sm"></i> Visit our Facebook page
            </a>
        </p>
    </footer>
    <script>
        function togglePassword() {
            let passwordInput = document.getElementById("password");
            let toggleIcon = document.querySelector(".toggle-password");
            
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                toggleIcon.textContent = "🙈"; // Change to "Hide"
            } else {
                passwordInput.type = "password";
                toggleIcon.textContent = "👁️"; // Change to "Show"
            }
        }
    </script>
    
</body>
</html>

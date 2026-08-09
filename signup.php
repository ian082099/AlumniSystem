<?php

require_once 'db_connect.php'; // Ensure this file path is correct
global $conn;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $last_name = $_POST['last_name'];
    $first_name = $_POST['first_name'];
    $middle_initial = $_POST['middle_initial'];
    $email = $_POST['email'];
    $course = $_POST['course'];
    $year_graduated = $_POST['year_graduated'];
    $batch = $_POST['batch'];
    
    // Map course names to IDs
    $course_mapping = [
        "CS" => 1,
        "IT" => 2,
        "ACT" => 3
    ];
    
    $course_id = $course_mapping[$course] ?? null;
    
    if ($course_id === null) {
        echo "<script>alert('Invalid course selected.'); window.location.href='signup.php';</script>";
        exit();
    }
    
    $sql = "INSERT INTO students (course_id, lastname, firstname, mi, email_address, sy_graduate, batch)
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssis", $course_id, $last_name, $first_name, $middle_initial, $email, $year_graduated, $batch);
    
    if ($stmt->execute()) {
        echo "<script>
    alert('Registration successful!');
    window.location.href='http://localhost/AlumniSystem/Users/REGISTAR/REGISTAR.php';
</script>";

    } else {
        echo "<script>alert('Error: Could not register.');</script>";
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
           background: url('background.png') no-repeat center center fixed;
            background-size: cover;
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
              margin: 0; /* ✅ Remove extra margins */
                transition: opacity 1s ease-in-out;
        }
        
        body.loaded {
            opacity: 1;
        }
        
         h1 {
    font-size: 50px;
    font-weight: bold; /* Makes the text bold */
    margin: 30px 0; /* Adjust margin (removes extra space on the right) */
    text-align: left; /* Aligns the text to the left */
}

        .signup-container {
            background: rgba(255, 0, 0, 0.3);
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(255, 255, 255, 0.2);
            width: 800px;
            text-align: center;
    opacity: 0;
    transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
        }
        .signup-container.loaded {
    transform: scale(1);  /* Expand effect */
    opacity: 1; /* Fade in */
}
/* Row layout */
.row {
    display: flex;
    gap: 15px;
    margin-bottom: 12px;
}
.input-container {
    display: flex;
    align-items: center;
    background: transparent;
    border: 2px solid white;
    padding: 12px;
    margin: 5px 0;
    border-radius: 5px;
    flex: 1;
     font-size: 16px;
}
.input-container.small {
    flex: 0.5; /* Middle Initial is smaller */
}
.input-container i {
    color: white;
    margin-right: 8px;
}

.input-container input,
.input-container select {
    background: transparent;
    border: none;
    color: white;
    width: 100%;
    outline: none;
    
}
        input,select{
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 2px solid white;
            background: transparent;
            color: white;
            border-radius: 5px;
            outline: none;  
        }
        
        select {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 2px solid white;
    background: black; /* Dropdown background */
    color: white;
    border-radius: 5px;
    outline: none;
    appearance: none;
}

/* Style dropdown options */
select option {
    background-color: black !important; /* Set background to black */
    color: white !important; /* Keep text white */
}
::placeholder {
    color: white !important;
    opacity: 1; /* Optional: makes placeholder fully opaque */
}
        
        button {
            width: 100%;
            padding: 10px;
            background: transparent;
            color: white;
            border: 2px solid white;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            margin-top: 15px;
            transition: background 0.3s, transform 0.2s;
        }
    .button-container {
    display: flex;
    justify-content: center;
    width: 100%;
    margin-top: 10px;
     gap: 15px;
}

button {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.back-button, 
.back-register {
      flex: 1;
    padding: 12px;
    text-align: center;
    background: transparent;
    color: white;
    border: 2px solid white;
    font-size: 16px;
    cursor: pointer;
    border-radius: 5px;
    transition: transform 0.2s, background 0.3s;
      width: 45%; /* Wider buttons */
    padding: 14px;
}
        .back-button:hover, 
.back-register:hover {
    transform: scale(1.1);
    background: green;
    color: white;
    font-weight: bold;
    
}
.header-container {
    display: flex;
    align-items: center; /* Align items vertically */
    justify-content: space-between; /* Push elements to the edges */
    width: 100%; /* Make sure it takes full width */
    padding: 10px 20px; /* Adjust padding as needed */
}
           .logo {
    width: 100px; /* Adjust size as needed */
    display: block;
    margin: 0 auto 15px; /* Center the logo and add space below */
    filter: invert(1);
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
   <div class="signup-container">
  <div class="header-container">
    <h1>Register here</h1>
    <img src="contract.png" alt="Cunanan Academy Logo" class="logo">
</div>

    <form method="POST" action="signup.php">
        
        <!-- First Row -->
        <div class="row">
            <div class="input-container">
                <i class="fas fa-user"></i>
                <input type="text" name="last_name" placeholder="Last Name" required>
            </div>
            <div class="input-container">
                <i class="fas fa-user"></i>
                <input type="text" name="first_name" placeholder="First Name" required>
            </div>
            <div class="input-container small">
                <i class="fas fa-user"></i>
                <input type="text" name="middle_initial" placeholder="M.I." maxlength="1" required>
            </div>
        </div>

        <!-- Second Row -->
        <div class="row">
            <div class="input-container">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email Address" required>
            </div>
            <div class="input-container">
                <i class="fas fa-graduation-cap"></i>
                <select name="course" required>
                    <option value="">Select Course</option>
                    <option value="CS">Computer Science (CS)</option>
                    <option value="IT">Information Technology (IT)</option>
                    <option value="ACT">Associate in Computer Technology (ACT)</option>
                </select>
            </div>
        </div>

        <!-- Third Row -->
        <div class="row">
            <div class="input-container">
                <i class="fas fa-calendar"></i>
                <input type="number" name="year_graduated" placeholder="Year Graduated" min="1900" max="2099" required>
            </div>
            <div class="input-container">
                <i class="fas fa-users"></i>
                <input type="text" name="batch" placeholder="Batch" required>
            </div>
        </div>

        <!-- Buttons Row -->
        <div class="button-container">
            <button type="submit" class="back-register">
                <i class="fas fa-user-plus"></i> Register
            </button>
            <button type="button" class="back-button" onclick="history.back()">
                <i class="fas fa-arrow-left"></i> Back
            </button>
        </div>
        
    </form>
</div>
    <footer>
        <p>
            <a href="https://www.google.com/maps/search/?api=1&query=University+Road+NBP+Reservation+Brgy.+Poblacion%2C+City+of+Muntinlupa%2C+Philippines%2C+1776" target="_blank">
                <i class="fas fa-map-marker-alt fa-sm"></i> University Road NBP Reservation Brgy. Poblacion, City of Muntinlupa. Philippines, 1776
            </a>
        </p>
        <p><i class="far fa-envelope fa-sm"></i> plmun.com@plmun.edu.ph</p>
        <p>
            <a href="https://www.facebook.com/PLMUN.BSCS.SOCIETY" target="_blank">
                <i class="fab fa-facebook-square fa-sm"></i> Visit our Facebook page
            </a>
        </p>
    </footer>
</body>
<script>
    window.onload = function () {
        document.body.classList.add('loaded');
        setTimeout(() => {
            document.querySelector('.signup-container').classList.add('loaded');
        }, 300);
    };
</script>

</html>

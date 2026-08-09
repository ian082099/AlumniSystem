<?php
require_once '../db_connect.php';
global $conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $uname = $_POST['uname'];
    $access = $_POST['access'];
    $password = $_POST['password'];

    // UCODE mapping
    $ucode_map = [
        "Dean" => 1,
        "Act" => 2,
        "IT" => 3,
        "CS" => 4,
        "Registar" => 0
    ];

    $ucode = $ucode_map[$uname] ?? null;

    if ($ucode === null || empty($uname) || empty($name) || empty($password) || empty($access)) {
        echo "<script>alert('Please complete all required fields properly.'); window.location.href='register.php';</script>";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO loginuserlevels (ACCESS, UCODE, UNAME, PASSWORD, Name) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sisss", $access, $ucode, $uname, $password, $name);

    if ($stmt->execute()) {
        // If the query executes successfully, show success message and redirect
        echo "<script>
                alert('Registration successful!');
                window.location.href = 'http://localhost/AlumniSystem/Login.php';
              </script>";
    } else {
        // If the query fails, show error message
        echo "<script>alert('Error: Registration failed.');</script>";
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background: url('../background.png') no-repeat center center fixed;

            background-size: cover;
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
              margin: 0; /* ✅ Remove extra margins */
        }
        * {
    box-sizing: border-box;
}

        .form-container {
            background: rgba(179, 0, 0, 0.85);
            padding: 60px 40px;
            max-width: 900px;
            margin: 60px auto;
            border-radius: 10px;
            color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
        }
        h2 {
            text-align: center;
            font-size: 36px;
            margin-bottom: 20px;
        }
        .form-group {
            display: flex;
            gap: 20px;
            margin-bottom: 25px;
        }
        .form-group > div {
            flex: 1;
            position: relative;
        }
        input, select {
            width: 100%;
            border: none;
            border-radius: 5px;
            margin-bottom: 10px;
            margin: 0; /* Remove inner spacing */
    padding: 12px 40px 12px 12px; /* Slightly more breathing room */
    font-size: 16px;
        }
        .form-group i {
            position: absolute;
            right: 10px;
            top: 10px;
            color: #333;
        }
        .form-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
        button {
            flex: 1;
            padding: 12px;
            border: none;
            background: #fff;
            color: #b30000;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin: 0 5px; /* Give space between buttons */
            
        }
        button i {
            margin-right: 8px;
        }
        button:hover {
            background: #e6e6e6;
            transform: translateY(-2px);
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
input[type="password"] {
    padding-right: 40px; /* make space for the eye icon */
}


    </style>
</head>
<body>

<div class="form-container">
    <h2>Register here</h2>
    <form method="POST" action="">
        <div class="form-group">
            <div>
                <input type="text" name="name" placeholder="Full Name" required>
                <i class="fa fa-user"></i>
            </div>
            <div>
                <select name="access" id="access" required onchange="toggleUname()">
                    <option value="">Select Access</option>
                    <option value="CITCS">CITCS</option>
                    <option value="Others">Others</option>
                </select>
                <i class="fa fa-lock"></i>
            </div>
        </div>
        <div class="form-group">
            <div>
                <select name="uname" id="uname" required>
                    <option value="">Select Uname</option>
                    <option value="Dean">Dean</option>
                    <option value="Act">Act</option>
                    <option value="IT">IT</option>
                    <option value="CS">CS</option>
                    <option value="Registar">Registar</option>
                </select>
                <i class="fa fa-id-badge"></i>
            </div>
            <div style="position: relative; width: 100%;">
    <input type="password" id="password" name="password" placeholder="Enter your Password" required style="padding-left: 30px; padding-right: 30px;">
    <span class="icon" style="position: absolute; left: 10px; top: 50%; transform: translateY(-50%); pointer-events: none;">🔒</span>
    <span class="toggle-password" onclick="togglePassword()" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
        👁️
    </span>
</div>

        </div>

        <div class="form-buttons">
            <button type="submit"><i class="fa fa-user-plus"></i>Register</button>
            <button type="button" onclick="location.href='http://localhost/AlumniSystem/Login.php'">
    <i class="fa fa-arrow-left"></i> Back
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
<script>
    function toggleUname() {
        const access = document.getElementById('access').value;
        const uname = document.getElementById('uname');
        uname.disabled = access !== "CITCS";
    }
    window.onload = () => {
        toggleUname();
    };
</script>
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

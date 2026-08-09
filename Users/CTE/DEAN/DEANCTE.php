<?php
namespace Users\CTE\DEAN;
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: /AlumniSystem/Login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dean's Dashboard</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: black;
            color: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
        }

        .dashboard-container {
            background: #111;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 0px 15px rgba(255, 255, 255, 0.2);
            width: 350px;
        }

        h2 {
            font-size: 24px;
            margin-bottom: 20px;
            color: white;
        }

        .logout-button {
            display: block;
            width: 100%;
            padding: 12px;
            background: white;
            color: black;
            border: none;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            border-radius: 5px;
            text-decoration: none;
            margin-top: 15px;
        }

        .logout-button:hover {
            background: #ccc;
        }

    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, DEAN OF THE CTE!</h2>
        <p>You are now logged into the system.</p>
        <a href="/AlumniSystem/Login.php" class="logout-button">Logout</a>
    </div>
</body>
</html>

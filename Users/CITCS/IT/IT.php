<?php
namespace Users\CITCS\IT;
use mysqli;
include '../../../filter.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['username'])) {
    header("Location: /AlumniSystem/Login.php");
    exit();
}
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : '';
$selectedBatch = isset($_GET['batch']) ? $_GET['batch'] : '';
$rowsPerPage = isset($_GET['rows']) ? intval($_GET['rows']) : 5; // ✅ Default 5 rows per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Ensure it's at least 1

// Calculate the offset for pagination
$offset = ($page - 1) * $rowsPerPage; // FIX: Define $offset before using it in SQL

// Database connection
$servername = "127.0.0.1"; // Assuming localhost
$username = "root"; // Change if you have a different username
$password = ""; // Change if you have a database password
$database = "dbjointable"; // Database name

$conn = new mysqli($servername, $username, $password, $database);


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT students.id, students.lastname, students.firstname, students.mi, students.email_address,
course.course_name, students.sy_graduate, students.batch
FROM students
INNER JOIN course ON students.course_id = course.id
WHERE course.id = 2"; // Base query already has a WHERE clause



$conditions = [];
if (!empty($selectedYear)) {
    $conditions[] = "students.sy_graduate = '$selectedYear'";
}
if (!empty($selectedBatch)) {
    $conditions[] = "students.batch = '$selectedBatch'";
}

if (!empty($conditions)) {
    $sql .= " AND " . implode(" AND ", $conditions); // Use AND instead of WHERE
}

$sql .= " ORDER BY students.sy_graduate DESC LIMIT $offset, $rowsPerPage";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);  // Debugging
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

background: url('/AlumniSystem/Users/CITCS/IT/background.png') no-repeat center center fixed;
background-size: cover;
    color: white;
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
      transition: opacity 1s ease-in-out;
}
        body.loaded .dashboard-container {
    opacity: 1;
    transform: scale(1);
}
        .dashboard-container {
            background: rgba(17, 17, 17, 0.5);
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0px 0px 15px rgba(255, 255, 255, 0.2);
            width: 1000px;
            text-align: center;
            backdrop-filter: blur(5px); 
              opacity: 0;
    transform: scale(0.8);
    transition: transform 0.5s ease-in-out, opacity 0.5s ease-in-out;
        }




  h2 {
    text-align: center; 
    font-size: 28px;
    font-weight: bold;
    margin-bottom: 20px;
}

h1 {
    text-align: left; 
    font-size: 20px;
    margin-bottom: 10px;
}
 

    
 table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid white;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: rgba(255, 255, 255, 0.2);
        }
   .logout-button {
    display: inline-block;
    padding: 8px 16px; /* Reduce padding for a smaller button */
    background: red;
    color: white;
    border: 1px solid black; /* Add a border for a more standard look */
    font-size: 14px; /* Reduce font size */
    font-weight: normal; /* Remove bold effect */
    cursor: pointer;
    border-radius: 3px; /* Slightly rounded corners */
    text-decoration: none;
    margin-top: 10px; /* Reduce margin */
    transition: background 0.3s ease-in-out, transform 0.2s;
}

        .logout-button:hover {
            background: green;
             transform: scale(1.05);
        }
/* Dropdown styling */
.dropdown {
    margin-right: 20px; /* Adjust spacing between dropdowns */
    padding: 5px; /* Optional: Add padding for better appearance */
    padding: 8px;
    font-size: 16px;
    width: 200px; /* Adjust width as needed */
    border-radius: 5px;
    border: 1px solid rgba(255, 255, 255, 0.5); /* Semi-transparent border */
    background-color: rgba(255, 255, 255, 0.2); /* Transparent background */
    color: white; /* Text color */
    backdrop-filter: blur(5px); /* Glass effect */
    cursor: pointer;
}

/* Change arrow color */
.dropdown option {
    background-color: black; /* Dropdown options background */
    color: white; /* Dropdown options text */
}

/* Hover effect */
.dropdown:hover {
    background-color: rgba(255, 255, 255, 0.3);
}

/* Focus (when clicked) */
.dropdown:focus {
    outline: none;
    border-color: white;
}
/* Focus (when clicked) */
.dropdown:focus {
    outline: none;
    border-color: white;
}
.pagination {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

.page-link {
    display: inline-block;
    padding: 8px 12px;
    margin: 0 5px;
    text-decoration: none;
    color: #fff;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 5px;
    transition: 0.3s;
}

.page-link:hover {
    background: rgba(255, 255, 255, 0.4);
}

.page-link.active {
    font-weight: bold;
    text-decoration: underline;
    background: #7d7d7d;
}
    </style>
</head>
<body onload="setTimeout(() => document.body.classList.add('loaded'), 100);">
    <div class="dashboard-container">
        <h2>Welcome, IT STUDENTS!</h2>
        <p>You are now logged into the system.</p>
        
        <h1>List of Students</h1>
 <form method="GET" action="<?= $_SERVER['PHP_SELF']; ?>">
            <div class="dropdown-container">
                
                <label>Rows:</label>
                <select name="rows" class="dropdown" onchange="this.form.submit()">
                    <option value="5" <?= $rowsPerPage == 5 ? 'selected' : '' ?>>5</option>
                    <option value="10" <?= $rowsPerPage == 10 ? 'selected' : '' ?>>10</option>
                    <option value="20" <?= $rowsPerPage == 20 ? 'selected' : '' ?>>20</option>
                    <option value="50" <?= $rowsPerPage == 50 ? 'selected' : '' ?>>50</option>
                </select>
                <?php getYearDropdown(); ?>
                <?php getBatchDropdown(); ?>
               
            </div>
        </form>
<table>
    <tr>
        <th>Number of Student</th>
        <th>ALUMNI ID</th>
        <th>Lastname</th>
        <th>Firstname</th>
        <th>Middle Initial</th>
        <th>Email</th>
        <th>Course</th>
        <th>Year</th>
        <th>Batch</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php
        $counter = $offset + 1;
        while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $counter++; ?></td>
                <td><?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td> <!-- ✅ Format Alumni ID -->
                <td><?= $row['lastname'] ?></td>
                <td><?= $row['firstname'] ?></td>
                <td><?= $row['mi'] ?></td>
                <td><?= $row['email_address'] ?></td>
                <td><?= $row['course_name'] ?></td>
                <td><?= $row['sy_graduate'] ?></td>
                <td><?= $row['batch'] ?></td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="9">No students found</td></tr>
    <?php endif; ?>
</table>

        <?php
// Count total records for pagination
$countQuery = "SELECT COUNT(*) AS total FROM students INNER JOIN course ON students.course_id = course.id";
if (!empty($conditions)) {
    $countQuery .= " WHERE " . implode(" AND ", $conditions);
}
$countResult = $conn->query($countQuery);
$totalRows = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalRows / $rowsPerPage);
?>

<!-- Pagination Section -->
<div class="pagination">
    <?php if ($page > 1): ?>
        <a href="?rows=<?= $rowsPerPage ?>&year=<?= $selectedYear ?>&batch=<?= $selectedBatch ?>&page=<?= $page - 1 ?>" class="page-link">&laquo;</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?rows=<?= $rowsPerPage ?>&year=<?= $selectedYear ?>&batch=<?= $selectedBatch ?>&page=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?rows=<?= $rowsPerPage ?>&year=<?= $selectedYear ?>&batch=<?= $selectedBatch ?>&page=<?= $page + 1 ?>" class="page-link">&raquo;</a>
    <?php endif; ?>
</div>
        
        <a href="/AlumniSystem/Login.php" class="logout-button">Logout</a>
    </div>
</body>
</html>

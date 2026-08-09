<?php
namespace Users\CITCS\DEAN;
use mysqli;
include '../../../filter.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


if (!isset($_SESSION['username'])) {
    header("Location: /AlumniSystem/Login.php");
    exit();
}

// Get the selected year from GET request
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : '';
$selectedBatch = isset($_GET['batch']) ? $_GET['batch'] : '';
$selectedCourse = isset($_GET['course']) ? $_GET['course'] : '';
$rowsPerPage = isset($_GET['rows']) ? intval($_GET['rows']) : 5; // ✅ Default 5 rows per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Ensure it's at least 1

// Calculate the offset for pagination
$offset = ($page - 1) * $rowsPerPage; // FIX: Define $offset before using it in SQL
// Database connection
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "dbjointable";

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Base SQL query
$sql = "SELECT students.id, students.lastname, students.firstname, students.mi, students.email_address,
               course.course_name, students.sy_graduate, students.batch
        FROM students
        INNER JOIN course ON students.course_id = course.id";

$conditions = [];
if (!empty($selectedYear)) {
    $conditions[] = "students.sy_graduate = '$selectedYear'";
}
if (!empty($selectedBatch)) {
    $conditions[] = "students.batch = '$selectedBatch'";
}
if (!empty($selectedCourse)) {
    $conditions[] = "course.course_name = '$selectedCourse'";
}

if (!empty($conditions)) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY students.sy_graduate DESC LIMIT $offset, $rowsPerPage";

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);  // Debugging
}
$chartData = [];
$query = "SELECT sy_graduate, COUNT(*) AS total FROM students GROUP BY sy_graduate ORDER BY sy_graduate DESC";
$chartResult = $conn->query($query);

while ($row = $chartResult->fetch_assoc()) {
    $chartData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="/AlumniSystem/Users/CITCS/DEAN/Dean_style.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.3.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <title>Dean's Dashboard</title>
    <style>
      
    </style>
</head>
<body onload="setTimeout(() => document.body.classList.add('loaded'), 100);">

    
    <div class="dashboard-container">
        <h2>Welcome, Dean!</h2>
      
         <div class="tabs">
            <div class="tab active" onclick="switchTab('list')">Students List</div>
            <div class="tab" onclick="switchTab('graph')">Analysis</div>
        </div>
        
        <div id="list" class="tab-content active">
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
        <?php getCourseDropdown(); ?>
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
        <a href="?rows=<?= $rowsPerPage ?>&year=<?= $selectedYear ?>&batch=<?= $selectedBatch ?>&course=<?= $selectedCourse ?>&page=<?= $page - 1 ?>" class="page-link">&laquo;</a>
    <?php endif; ?>

    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <a href="?rows=<?= $rowsPerPage ?>&year=<?= $selectedYear ?>&batch=<?= $selectedBatch ?>&course=<?= $selectedCourse ?>&page=<?= $i ?>" class="page-link <?= $i == $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>

    <?php if ($page < $totalPages): ?>
        <a href="?rows=<?= $rowsPerPage ?>&year=<?= $selectedYear ?>&batch=<?= $selectedBatch ?>&course=<?= $selectedCourse ?>&page=<?= $page + 1 ?>" class="page-link">&raquo;</a>
    <?php endif; ?>
</div>
       </div>
        <div id="graph" class="tab-content">
    <h2>Student Analysis</h2>

<div class="charts-container">

        <canvas id="studentChart" style="width: 50%; max-width: 700px;"></canvas>
    <canvas id="studentPieChart" style="width: 20%; max-width: 500px; height: auto;"></canvas>
    </div>

</div>

           <a href="/AlumniSystem/Login.php" class="logout-button">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
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
</body>
<script>
function switchTab(tabId) {
    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.remove('active'));
    document.querySelectorAll('.tab').forEach(tab => tab.classList.remove('active'));
    document.getElementById(tabId).classList.add('active');
    document.querySelector(`[onclick="switchTab('${tabId}')"]`).classList.add('active');

    // Scroll to top when switching tabs
    window.scrollTo({ top: 0, behavior: 'smooth' });

    // 📌 Stretch dashboard when switching to Analysis tab
    let dashboard = document.querySelector('.dashboard-container');
    if (tabId === 'graph') {
        dashboard.style.width = '1400px';  // Expand width
        dashboard.style.height = 'auto';   // Adjust height dynamically
        dashboard.style.padding = '50px';
    } else {
        dashboard.style.width = '1200px';  // Restore default width
        dashboard.style.height = 'auto';
        dashboard.style.padding = '40px';
    }
}

    document.addEventListener('DOMContentLoaded', function() {
        const labels = <?= json_encode(array_column($chartData, 'sy_graduate')) ?>;
        const dataValues = <?= json_encode(array_column($chartData, 'total')) ?>;
        const totalStudents = <?= array_sum(array_column($chartData, 'total')) ?>;

        // 🎯 Bar Chart with White Labels
        const ctxBar = document.getElementById('studentChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Students per Year',
                    data: dataValues,
                    backgroundColor: 'rgba(0, 255, 0, 1)', 
      				borderColor: 'rgba(255, 0, 0, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        labels: {
                            color: '#ffffff'  // ✅ White legend text
                        }
                    },
                    tooltip: {
                        titleColor: '#ffffff',  
                        bodyColor: '#ffffff'  
                    }
                },
                scales: {
                    x: {
                        ticks: {
                            color: '#ffffff'  // ✅ White Year Level Labels (X-axis)
                        }
                    },
                    y: {
                        ticks: {
                            color: '#ffffff'  // ✅ White Numbers on Y-axis
                        }
                    }
                }
            }
        });

        // 🍕 Pie Chart with White Labels
        const ctxPie = document.getElementById('studentPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: labels,
                datasets: [{
                    data: dataValues,
                    backgroundColor: [
                        '#1f77b4', // Blue
                        '#ff7f0e', // Orange
                        '#2ca02c', // Green
                        '#d62728', // Red
                        '#9467bd', // Purple
                        '#8c564b'  // Brown
                    ],
                    borderColor: '#ffffff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                 maintainAspectRatio: false, 
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#ffffff', // ✅ White legend text (Year Levels)
                            font: { size: 14 }
                        }
                    },
                    tooltip: {
                        titleColor: '#ffffff',
                        bodyColor: '#ffffff',
                        callbacks: {
                            label: function(tooltipItem) {
                                let value = tooltipItem.raw;
                                let percentage = ((value / totalStudents) * 100).toFixed(1);
                                return `${tooltipItem.label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>







</html>

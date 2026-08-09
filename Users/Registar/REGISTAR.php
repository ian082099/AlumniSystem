<?php
namespace Users\REGISTAR;
use mysqli;
include '../../filter.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: /AlumniSystem/Login.php");
    exit();
}

// ✅ Move Database Connection to the Top
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "dbjointable";

$conn = new mysqli($servername, $username, $password, $database);

// ✅ Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ✅ Now, the connection is available for delete queries
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {
    $deleteId = intval($_POST["delete_id"]);
    $deleteQuery = "DELETE FROM students WHERE id = $deleteId";
    
    if ($conn->query($deleteQuery) === TRUE) {
        echo "success";
    } else {
        echo "error";
    }
    exit(); // Stop further execution to return response only
}

// Get selected filters from GET request
$selectedYear = isset($_GET['year']) ? intval($_GET['year']) : '';
$selectedBatch = isset($_GET['batch']) ? $_GET['batch'] : '';
$selectedCourse = isset($_GET['course']) ? $_GET['course'] : '';
$rowsPerPage = isset($_GET['rows']) ? intval($_GET['rows']) : 5; // ✅ Default 5 rows per page
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1; // Ensure it's at least 1

// Calculate the offset for pagination
$offset = ($page - 1) * $rowsPerPage;

// ✅ Now, run the main SQL query
$sql = "SELECT students.id, students.lastname, students.firstname, students.mi, students.email_address,
               course.course_name, students.sy_graduate, students.batch
        FROM students
        INNER JOIN course ON students.course_id = course.id";

// Apply filters dynamically
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <link href="/AlumniSystem/Users/REGISTAR/Registarstyle.css" rel="stylesheet">
<style>
 .header-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px;
}   

.add-alumni-container {
    margin-top: 10px;
   
    display: flex;
    justify-content: flex-start;

}

.add-button {
    background-color: #28a745;
    color: white;
    padding: 10px 20px;
    font-size: 16px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.add-button:hover {
    background-color: maroon; /* Darker green */
    transform: translateY(-2px);
}
h2 {
    flex: 1;
    text-align: right; /* Align text to the left */
    font-size: 35px;
    margin-right: 300px; /* Add left margin to move it 3 steps to the left */
   
    color: white;
}

</style>
    <title>Registar Page</title>
    
</head>
<body onload="setTimeout(() => document.body.classList.add('loaded'), 100);">
    <div class="dashboard-container">
        <div class="header-wrapper">
        <h2>HELLO REGISTAR!!</h2>
        <div class="add-alumni-container">
            <a href="http://localhost/AlumniSystem/signup.php">
                <button class="add-button">Add New Alumni</button>
            </a>
        </div>
    </div>


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
        <th>Number</th>
        <th>ALUMNI ID</th>
        <th>Lastname</th>
        <th>Firstname</th>
        <th>Middle Initial</th>
        <th>Email Address</th>
        <th>Course</th>
        <th>Year</th>
        <th>Batch</th>
        <th>Actions</th> <!-- ✅ New column -->
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php
        $counter = $offset + 1;
        while ($row = $result->fetch_assoc()): ?>
            <tr id="row-<?= $row['id'] ?>">
                <td><?= $counter++; ?></td>
                <td><?= str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></td>
                <td><?= $row['lastname'] ?></td>
                <td><?= $row['firstname'] ?></td>
                <td><?= $row['mi'] ?></td>
                <td><?= $row['email_address'] ?></td>
                <td><?= $row['course_name'] ?></td>
                <td><?= $row['sy_graduate'] ?></td>
                <td><?= $row['batch'] ?></td>
               <td>
    <button class="edit-button"  onclick="editRecord(
        '<?= $row['id'] ?>',
        '<?= addslashes($row['lastname']) ?>',
        '<?= addslashes($row['firstname']) ?>',
        '<?= addslashes($row['mi']) ?>',
        '<?= addslashes($row['email_address']) ?>',
        '<?= addslashes($row['course_name']) ?>',
        '<?= addslashes($row['sy_graduate']) ?>',
        '<?= addslashes($row['batch']) ?>'
    )">Edit</button>

   <button class="delete-button" onclick="deleteStudent(<?= $row['id'] ?>)">Delete</button>
</td>

            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="10">No students found</td></tr>
    <?php endif; ?>
</table>

<div id="modalOverlay"></div>

<!-- Modal -->
<div id="editModal">
  <h1>Student Form</h1>
   <form id="editForm" method="POST" action="http://localhost/AlumniSystem/Users/REGISTAR/edit_student.php">
  
        <input type="hidden" id="edit_id" name="edit_id">
        
        <label>Last Name</label>
        <input type="text" id="edit_lastname" name="edit_lastname">
        
        <label>First Name</label>
        <input type="text" id="edit_firstname" name="edit_firstname">
        
        <label>Middle Initial</label>
        <input type="text" id="edit_mi" name="edit_mi">
        
        <label>Email</label>
        <input type="email" id="edit_email" name="edit_email">
        
        <label>Course</label>
        <input type="text" id="edit_course" name="edit_course">
        
        <label>Year Graduated</label>
        <input type="text" id="edit_year" name="edit_year">
        
        <label>Batch</label>
        <input type="text" id="edit_batch" name="edit_batch">
        
        <div class="modal-buttons">
            <button type="submit">Update</button>
            <button type="button" onclick="closeEditModal()">Cancel</button>
        </div>
    </form>
</div>

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

<div class="pagination-container">
    <!-- Left: Download Button -->
    <div class="download-btn-container">
        <a href="generate_pdf.php" target="_blank" class="download-btn">Download PDF</a>
    </div>

    <!-- Center: Pagination -->
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

    <!-- Right: Logout Button -->
    <a href="/AlumniSystem/Login.php" class="logout-button">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
 
</div>


</body>
<script>
function editRecord(id, lastname, firstname, mi, email, course, year, batch) {
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_lastname').value = lastname;
    document.getElementById('edit_firstname').value = firstname;
    document.getElementById('edit_mi').value = mi;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_course').value = course;
    document.getElementById('edit_year').value = year;
    document.getElementById('edit_batch').value = batch;

    // Show modal and overlay
    document.getElementById('editModal').style.display = 'block';
    document.getElementById('modalOverlay').style.display = 'block';
}

function closeEditModal() {
    // Hide modal and overlay
    document.getElementById('editModal').style.display = 'none';
    document.getElementById('modalOverlay').style.display = 'none';
}
</script>


<script>
function deleteStudent(studentId) {
    if (confirm("Are you sure you want to delete this row?")) {
        fetch("<?= $_SERVER['PHP_SELF']; ?>", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "delete_id=" + studentId
        })
        .then(response => response.text())
        .then(data => {
            if (data === "success") {
                document.getElementById("row-" + studentId).remove(); // Remove row from table
                alert("Student deleted successfully!");
            } else {
                alert("Failed to delete student.");
            }
        })
        .catch(error => console.error("Error:", error));
    }
}
</script>

</html>

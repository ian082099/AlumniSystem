<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ✅ Database Connection Function
function getDatabaseConnection() {
    $conn = new mysqli("127.0.0.1", "root", "", "dbjointable");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// ✅ Year Dropdown Function
function getYearDropdown() {
    $conn = getDatabaseConnection();
    $query = "SELECT DISTINCT sy_graduate FROM students ORDER BY sy_graduate DESC";
    $result = $conn->query($query);
    
    $selectedYear = isset($_GET['year']) ? $_GET['year'] : '';
    
    echo '<select name="year" id="year" class="dropdown" onchange="this.form.submit()">';
    echo '<option value="">-- Select Year --</option>';
    
    while ($row = $result->fetch_assoc()) {
        $year = $row['sy_graduate'];
        $selected = ($year == $selectedYear) ? 'selected' : '';
        echo "<option value='$year' $selected>$year</option>";
    }
    
    echo '</select>';
    $conn->close();
}

// ✅ Batch Dropdown Function
function getBatchDropdown() {
    $conn = getDatabaseConnection();
    $query = "SELECT DISTINCT batch FROM students ORDER BY batch DESC";
    $result = $conn->query($query);
    
    $selectedBatch = isset($_GET['batch']) ? $_GET['batch'] : '';
    
    echo '<select name="batch" id="batch" class="dropdown" onchange="this.form.submit()">';
    echo '<option value="">-- Select Batch --</option>';
    
    while ($row = $result->fetch_assoc()) {
        $batch = $row['batch'];
        $selected = ($batch == $selectedBatch) ? 'selected' : '';
        echo "<option value='$batch' $selected>$batch</option>";
    }
    
    echo '</select>';
    $conn->close();
}

// ✅ Course Dropdown Function
function getCourseDropdown() {
    $conn = getDatabaseConnection();
    $query = "SELECT DISTINCT course_name FROM course ORDER BY course_name ASC";
    $result = $conn->query($query);
    
    $selectedCourse = isset($_GET['course']) ? $_GET['course'] : '';
    
    echo '<select name="course" id="course" class="dropdown" onchange="this.form.submit()">';
    echo '<option value="">-- Select Course --</option>';
    
    while ($row = $result->fetch_assoc()) {
        $course = $row['course_name'];
        $selected = ($course == $selectedCourse) ? 'selected' : '';
        echo "<option value='$course' $selected>$course</option>";
    }
    
    echo '</select>';
    $conn->close();
}
?>

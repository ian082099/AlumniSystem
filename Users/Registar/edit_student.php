<?php
namespace Users\REGISTAR;
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "dbjointable";

$conn = new \mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = intval($_POST["edit_id"]);
    $lastname = $_POST["edit_lastname"];
    $firstname = $_POST["edit_firstname"];
    $mi = $_POST["edit_mi"];
    $email = $_POST["edit_email"];
    $course = $_POST["edit_course"];
    $year = $_POST["edit_year"];
    $batch = $_POST["edit_batch"];
    
    $sql = "UPDATE students SET
            lastname = '$lastname',
            firstname = '$firstname',
            mi = '$mi',
            email_address = '$email',
            course_id = (SELECT id FROM course WHERE course_name = '$course' LIMIT 1),
            sy_graduate = '$year',
            batch = '$batch'
            WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        header("Location: REGISTAR.php"); // Redirect back to the main page
        exit();
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

$conn->close();
?>

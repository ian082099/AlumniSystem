<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/vendor/autoload.php';  // Adjust path to match the location of the vendor folder


// Database Connection
$servername = "127.0.0.1";
$username = "root";
$password = "";
$database = "dbjointable";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all student records
$sql = "SELECT students.id, students.lastname, students.firstname, students.mi,
               students.email_address, course.course_name, students.sy_graduate, students.batch
        FROM students
        INNER JOIN course ON students.course_id = course.id
        ORDER BY students.sy_graduate DESC";

$result = $conn->query($sql);

// Create PDF
$pdf = new TCPDF();
$pdf->SetAutoPageBreak(true, 10);

// Set page orientation to landscape
$pdf->AddPage('L');  // 'L' stands for landscape

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'Student List Report', 0, 1, 'C');
$pdf->SetFont('helvetica', '', 12);

// Table Header
$pdf->Ln(5);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(15, 10, 'ID', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'Last Name', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'First Name', 1, 0, 'C', true);
$pdf->Cell(15, 10, 'MI', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'Email', 1, 0, 'C', true);
$pdf->Cell(35, 10, 'Course', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Year', 1, 0, 'C', true);
$pdf->Cell(20, 10, 'Batch', 1, 1, 'C', true);

// Table Content
$pdf->SetFont('helvetica', '', 10);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(15, 10, $row['id'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['lastname'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['firstname'], 1, 0, 'C');
    $pdf->Cell(15, 10, $row['mi'], 1, 0, 'C');
    $pdf->Cell(50, 10, $row['email_address'], 1, 0, 'C');
    $pdf->Cell(35, 10, $row['course_name'], 1, 0, 'C');
    $pdf->Cell(20, 10, $row['sy_graduate'], 1, 0, 'C');
    $pdf->Cell(20, 10, $row['batch'], 1, 1, 'C');
}

// Ensure "files" folder exists
$filesDir = __DIR__ . '/files';
if (!is_dir($filesDir)) {
    mkdir($filesDir, 0777, true);
}

// Save PDF
$pdfFilePath = $filesDir . '/student_list.pdf';
$pdf->Output($pdfFilePath, 'F');

$conn->close();

// Redirect to download the file
header("Location: files/student_list.pdf");
exit();
?>

<?php
include 'database.php';
session_start();
$username = $_SESSION['user_id'];

// Check if the user is logged in
if (empty($username)) {
    header("Location: index.php");
    exit;
}

// student
$stmt_student = $conn->prepare("SELECT * FROM student WHERE stud_ID = :username");
$stmt_student->bindParam(':username', $username);
$stmt_student->execute();
$student = $stmt_student->fetch(PDO::FETCH_ASSOC);

// lecturer
$stmt_lecturer = $conn->prepare("SELECT * FROM lecturer WHERE lect_ID = :username");
$stmt_lecturer->bindParam(':username', $username);
$stmt_lecturer->execute();
$lecturer = $stmt_lecturer->fetch(PDO::FETCH_ASSOC);

if (!empty($student)) {
    $userID = $student['stud_ID'];
    $userName = $student['stud_fName'];
} elseif (!empty($lecturer)) {
    $userID = $lecturer['lect_ID'];
    $userName = $lecturer['lect_fName'];
} else {
    // If the user ID is not found in either table, it's an invalid user
    header("Location: index.php");
    exit;
}
?>

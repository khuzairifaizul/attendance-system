<?php
include_once 'session.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: index.php");
} else {
  ("");
}

// Fetch subjects
$query_subject = "SELECT subject.sub_ID, subject.sub_fName
                  FROM subject
                  WHERE subject.lect_ID = :lecturer_id;";
$stmt_subject = $conn->prepare($query_subject);
$stmt_subject->bindParam(':lecturer_id', $userID);
$stmt_subject->execute();
$subjects = $stmt_subject->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- endof PHP, startof HTML -->

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FMKK Attendance System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    a {
      text-decoration: none;
    }
  </style>
</head>
<body>
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container px-lg-5">
      <a class="navbar-brand">FMKK ATTENDANCE SYSTEM</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link btn btn-danger" href="kill.php">Log out</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- Header-->
  <header class="py-5">
    <div class="container px-lg-5">
      <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
        <div class="m-4 m-lg-5">
          <h1 class="display-5 fw-bold">Welcome Lecturer 👋</h1>
          <p class="fs-4"><?php echo $userName; ?></p>
        </div>
      </div>
    </div>
  </header>
  <!-- Page Content-->
  <section class="pt-4">
    <a href=""></a>
    <div class="container px-lg-5">
      <div class="row gx-lg-5">
        <!-- Page Features-->
        <?php
        // Iterate through the subjects
        foreach ($subjects as $subject) {
          // Perform the query to fetch subject count for the current subject
          $query_student = "SELECT COUNT(DISTINCT student_class.stud_ID)
                          FROM lecturer
                          JOIN subject ON lecturer.lect_ID = subject.lect_ID
                          JOIN class ON subject.sub_ID = class.sub_ID
                          JOIN student_class ON class.class_ID = student_class.class_ID
                          WHERE lecturer.lect_ID = :lecturer_id
                          AND subject.sub_ID = :subject_id";
          $stmt_student = $conn->prepare($query_student);
          $stmt_student->bindParam(':lecturer_id', $userID);
          $stmt_student->bindParam(':subject_id', $subject['sub_ID']);
          $stmt_student->execute();
          $student = $stmt_student->fetchColumn();

          // Display the card
          echo '<div class="col-lg-6 col-xxl-4 mb-5">';
          echo '<a href="report.php?subject_id=' . $subject['sub_ID'] . '">';
          echo '<div class="card bg-light border-0 h-100">';
          echo '<div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">';
          echo '<div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4">';
          echo '<i class="bi bi-collection"></i>';
          echo '</div>';
          echo '<h2 class="fs-4 fw-bold">' . $subject['sub_fName'] . '</h2>';
          echo '<p class="mb-0">' . 'Student Enrolled: '  . $student . '</p>';
          echo '</div>';
          echo '</div>';
          echo '</a>';
          echo '</div>';
        }
        ?>
      </div>
    </div>
  </section>
  <!-- Bootstrap core JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Core theme JS-->
  <script src="js/scripts.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
include 'session.php';

// Perform the query to fetch subject names
$query = "SELECT subject.sub_fName
          FROM student_class
          JOIN class ON student_class.class_ID = class.class_ID
          JOIN subject ON class.sub_ID = subject.sub_ID
          WHERE student_class.stud_ID = :student_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':student_id', $userID);
$stmt->execute();
$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FMKK Attendance System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>

<body>

  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container px-lg-5">
      <a class="navbar-brand" href="#!">FMKK ATTENDANCE SYSTEM</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="#!">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="#!">Logout</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- Header-->
  <header class="py-5">
    <div class="container px-lg-5">
      <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
        <div class="m-4 m-lg-5">
          <h1 class="display-5 fw-bold">Welcome Student 👋</h1>
          <p class="fs-4"><?php echo $userName; ?></p>

        </div>
      </div>
    </div>
  </header>
  <!-- Page Content-->
  <section class="pt-4">
    <div class="container px-lg-5">
      <!-- Page Features-->
      <?php
      foreach ($subjects as $subject) {
        echo '<div class="row gx-lg-5">';
        echo '<div class="col-lg-6 col-xxl-4 mb-5">';
        echo '<div class="card bg-light border-0 h-100">';
        echo '<div class="card-body text-center p-4 p-lg-5 pt-0 pt-lg-0">';
        echo '<div class="feature bg-primary bg-gradient text-white rounded-3 mb-4 mt-n4">';
        echo '<i class="bi bi-collection"></i>';
        echo '</div>';
        echo '<h2 class="fs-4 fw-bold">' . $subject['sub_fName'] . '</h2>';
        // You can include additional information here if needed
        echo '<p class="mb-0">Absent Total: 3</p>'; // You might need to replace this with actual data
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
      }
      ?>


    </div>
  </section>

  <!-- Bootstrap core JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Core theme JS-->
  <script src="js/scripts.js"></script>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>
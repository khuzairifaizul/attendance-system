<?php
include 'session.php';

$query_subject = "SELECT subject.sub_ID
                  FROM subject
                  WHERE subject.lect_ID = :lecturer_id;";
$stmt_subject = $conn->prepare($query_subject);
$stmt_subject->bindParam(':lecturer_id', $userID);
$stmt_subject->execute();
$subjects = $stmt_subject->fetchAll(PDO::FETCH_ASSOC);

// Loop through the subjects
foreach ($subjects as $subject) {
  // Perform the query for attendance-log using the current subject
  $query_table = "SELECT student.stud_fName, `attendance-log`.weekNo, `attendance-log`.status
                    FROM `attendance-log`
                    JOIN student ON `attendance-log`.stud_ID = student.stud_ID
                    JOIN subject ON `attendance-log`.sub_ID = subject.sub_ID
                    JOIN class ON subject.sub_ID = class.sub_ID
                    WHERE class.lect_ID = :lecturer_id
                    AND `attendance-log`.weekNo BETWEEN 1 AND 14
                    AND subject.sub_ID = :subject_id;";
  $stmt_table = $conn->prepare($query_table);
  $stmt_table->bindParam(':lecturer_id', $userID);
  $stmt_table->bindParam(':subject_id', $subject['sub_ID']);

  // Execute the query for the current subject
  $stmt_table->execute();
  $table = $stmt_table->fetchAll(PDO::FETCH_ASSOC);
}
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
          <li class="nav-item"><a class="nav-link" href="#!">Attendance Logs</a></li>
          <li class="nav-item"><a class="nav-link" href="#!">Attendance Report</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- Header-->
  <header class="py-5">
    <div class="container px-lg-5">
      <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
        <h3>Attendance for Class</h3>
        <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
          <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
            <?php

            // Create an associative array to organize data by student and week
            $organizedData = [];
            foreach ($table as $row) {
              $studentName = $row['stud_fName'];
              $weekNo = $row['weekNo'];
              $status = $row['status'];

              if (!isset($organizedData[$studentName])) {
                $organizedData[$studentName] = [];
              }

              // Assign the status to the corresponding week
              $organizedData[$studentName][$weekNo] = $status;
            }

            // Display the table
            echo '<table class="table">';
            echo '<thead>';
            echo '<tr>';
            echo '<th>Name/Week</th>';

            // Display week numbers as table headers
            for ($week = 1; $week <= 14; $week++) {
              echo "<th>$week</th>";
            }

            echo '</tr>';
            echo '</thead>';
            echo '<tbody>';

            // Iterate through organized data
            foreach ($organizedData as $studentName => $weeks) {
              echo '<tr>';
              echo "<td>$studentName</td>";

              // Display 0/1 values for each week
              for ($weekNo = 1; $weekNo <= 14; $weekNo++) {
                if (isset($weeks[$weekNo])) {
                  if ($weeks[$weekNo] == 1) {
                    echo '<td style="color: green;">Present</td>';
                  } else {
                    echo '<td style="color: red;">Absent</td>';
                  }
                } else {
                  echo '<td>-</td>';
                }
              }

              echo '</tr>';
            }

            echo '</tbody>';
            echo '</table>';
            ?>
          </div>

        </div>

      </div>

    </div>
  </header>
  <!-- Bootstrap core JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Core theme JS-->
  <script src="js/scripts.js"></script>



  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>

</html>
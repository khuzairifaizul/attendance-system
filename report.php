<?php
include 'session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Check if the form is submitted
  if (isset($_POST['status'])) {
    $statusData = $_POST['status'];
    // Loop through the submitted status data
    foreach ($statusData as $studentName => $weeks) {
      foreach ($weeks as $weekNo => $status) {
        // Convert $status to an integer (0 or 1)
        $status = ($status == '0') ? 0 : 1;

        // Prepare and execute the SQL update statement
        $query = "UPDATE `attendance-log` 
                      SET status = :status 
                      WHERE stud_ID = (SELECT stud_ID FROM student WHERE stud_fName = :studentName)
                      AND weekNo = :weekNo
                      AND sub_ID = :subjectID";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':studentName', $studentName);
        $stmt->bindParam(':weekNo', $weekNo);
        $stmt->bindParam(':subjectID', $subject['sub_ID']);  // Use the correct subject identifier

        $stmt->execute();
      }
    }
    // Redirect or display a success message after updating the database
    header("Location: report.php");
    exit();
  }
}

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
// Initialize an empty array to organize data by student and week
$organizedData = [];

// Iterate through $table and organize the data
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
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FMKK Attendance System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container px-lg-5">
      <a class="navbar-brand" href="#!">FMKK ATTENDANCE SYSTEM</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
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
        <div class="p-4 p-lg-5 bg-light rounded-3 text-center table-responsive">
          <form method="post" action="report.php">
            <table class="table" style="width: 100%;">
              <thead>
                <tr>
                  <th>Name/Week</th>
                  <!-- Display week numbers as table headers -->
                  <?php for ($week = 1; $week <= 14; $week++) : ?>
                    <th><?php echo $week; ?></th>
                  <?php endfor; ?>
                </tr>
              </thead>
              <tbody>
                <!-- Iterate through organized data -->
                <?php foreach ($organizedData as $studentName => $weeks) : ?>
                  <tr>
                    <td><?php echo $studentName; ?></td>
                    <!-- Display dropdown menu for each week -->
                    <?php for ($weekNo = 1; $weekNo <= 14; $weekNo++) : ?>
                      <td>
                        <select name="status[<?php echo $studentName; ?>][<?php echo $weekNo; ?>]">
                          <option value="-">-</option>
                          <option value="0" <?php echo ($weeks[$weekNo] == 0) ? 'selected' : ''; ?> style="color: red;">Absent</option>
                          <option value="1" <?php echo ($weeks[$weekNo] == 1) ? 'selected' : ''; ?> style="color: green;">Present</option>
                        </select>
                      </td>
                    <?php endfor; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <input type="submit" value="Update Status">
          </form>
        </div>
      </div>
    </div>
  </header>
  <!-- Bootstrap core JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Core theme JS-->
  <script src="js/scripts.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
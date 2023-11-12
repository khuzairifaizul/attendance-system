<?php
include 'session.php';

$selectedSubjectID = isset($_GET['subject_id']) ? $_GET['subject_id'] : null;

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
  try {
    $conn->beginTransaction();

    $statusData = $_POST['status'];

    foreach ($statusData as $studentName => $weeks) {
      foreach ($weeks as $weekNo => $status) {
        $status = ($status == '0') ? 0 : 1;

        $query = "UPDATE `attendance-log`
                  SET status = :status
                  WHERE stud_ID IN (SELECT stud_ID FROM student WHERE stud_fName = :studentName)
                  AND weekNo = :weekNo
                  AND sub_ID = :subject_id";

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':studentName', $studentName);
        $stmt->bindParam(':weekNo', $weekNo);
        $stmt->bindParam(':subject_id', $selectedSubjectID);

        $stmt->execute();
      }
    }

    $conn->commit();
    header("Location: report.php?subject_id=$selectedSubjectID");
    exit();
  } catch (PDOException $e) {
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
  }
}

$query_subject = "SELECT subject.sub_ID
                  FROM subject
                  WHERE subject.lect_ID = :lecturer_id;";
$stmt_subject = $conn->prepare($query_subject);
$stmt_subject->bindParam(':lecturer_id', $userID);
$stmt_subject->execute();
$subjects = $stmt_subject->fetchAll(PDO::FETCH_ASSOC);

// Fetch the subject outside the loop
$subject = null;
foreach ($subjects as $subj) {
  if ($subj['sub_ID'] == $selectedSubjectID) {
    $subject = $subj;
    break;
  }
}

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
  $stmt_table->bindParam(':subject_id', $selectedSubjectID);

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

<!-- endof PHP, startof HTML -->

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>FMKK Attendance System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
  <style>
      body{
            background-image:url('bgO.png');
            background-attachment:fixed;
            background-size:100% 100%;
          }
  </style>
</head>
<body>
  <!-- Responsive navbar-->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container px-lg-6">
      <a class="navbar-brand">FMKK ATTENDANCE SYSTEM</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="index-lect.php">Home</a></li>
          <li class="nav-item"><a class="nav-link btn btn-danger" href="kill.php">Log out</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- Header-->
  <header class="col-md-12 p-5">
    <div class="container-fluid px-lg-5">
      <div class="p-4 p-lg-5 bg-light rounded-3 text-center">
        <h3>Attendance for Class <?php echo $selectedSubjectID ?></h3>
        
        <div class="row m-1 m-2">
          <div class= "col-sm-8">
            <div class="col-sm-4">
                <label for="searchInput" class="form-label visually-hidden">Search Student:</label>
              <div class="input-group">
                <input type="text" class="form-control" id="searchInput" placeholder="Search student...">
                <button class="btn btn-dark" id="searchButton" type="button">Search</button>
              </div>
            </div>
          </div>
        </div>
        <div class="p-2 bg-light rounded-3 text-center table-responsive" id="attendance-form">
          <form method="post" action="report.php?subject_id=<?php echo $selectedSubjectID; ?>">
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
                  <?php $absentCount = count(array_filter($weeks, function ($status) {
                          return $status == 0;
                        }));
                        if ($absentCount >= 3 && $absentCount <= 4) {
                          $nameStyle = 'color: orange;';
                        } elseif ($absentCount >= 5) {
                          $nameStyle = 'color: red;';
                        } else {
                          $nameStyle = ''; // No change in color
                        }
                      ?>
                  <tr>
                    <td style="<?php echo $nameStyle; ?>">
                    <?php echo $studentName; ?></td>
                      
                    <!-- Display dropdown menu for each week -->
                    <?php for ($weekNo = 1; $weekNo <= 14; $weekNo++) : ?>
                      <td>
                        <select name="status[<?php echo $studentName; ?>][<?php echo $weekNo; ?>]">
                          <option value="0" <?php echo ($weeks[$weekNo] == 0) ? 'selected' : ''; ?> style="color: red;">Absent</option>
                          <option value="1" <?php echo ($weeks[$weekNo] == 1) ? 'selected' : ''; ?> style="color: green;">Present</option>
                        </select>
                      </td>
                    <?php endfor; ?>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
            <div class="d-flex justify-content-end">
              <a class="btn btn-outline-danger me-2" type="reset" href="report.php?subject_id=<?php echo $selectedSubjectID; ?>">Reset</a>
              <input class="btn btn-dark pl-2" type="submit" value="Update Attendance">
            </div>
          </form>
        </div>
      </div>
    </div>
  </header>

  <script>
  // JavaScript code to handle the search functionality
  document.getElementById('searchButton').addEventListener('click', function () {
    var searchInput = document.getElementById('searchInput').value.toLowerCase();

    // Iterate through table rows and hide/show based on the search input
    var tableRows = document.querySelectorAll('#attendance-form tbody tr');
    tableRows.forEach(function (row) {
      var studentName = row.querySelector('td:first-child').textContent.toLowerCase();
      row.style.display = (studentName.includes(searchInput)) ? '' : 'none';
    });
  });
</script>
  <!-- Bootstrap core JS-->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- Core theme JS-->
  <script src="js/scripts.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
include "database.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    // Check if the user is a student
    $query = "SELECT * FROM student WHERE stud_fMail = :username AND stud_fPwd = :password";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $password);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    // If not a student, check if the user is a lecturer
    if (!$student) {
        $query = "SELECT * FROM lecturer WHERE lect_fMail = :username AND lect_fPwd = :password";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $lecturer = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($lecturer) {
            // Set the user information in the session for the lecturer
            $_SESSION['user_id'] = $lecturer['lect_ID'];
            $_SESSION['user_name'] = $lecturer['lect_fName'];

            // Redirect to lecturer dashboard or any other page
            header("Location: index-lect.php");
        } else {
            echo "Invalid username or password.";
        }
    } else {
        // Set the user information in the session for the student
        $_SESSION['user_id'] = $student['stud_ID'];
        $_SESSION['user_name'] = $student['stud_fName'];

        // Redirect to student dashboard or any other page
        header("Location: index-stud.php");
    }
}
?>

<!-- endof PHP, startof HTML -->

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Student Attendance System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap core CSS -->
  <style>
      body{
            background-image:url('bgO3.png');
            background-attachment:fixed;
            background-size:100% 100%;
      }

  </style>
</head>

<body>
  <section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
          <div class="card text-dark">
            <div class="card-body bg-light rounded text-dark p-5 text-center">
            <form action="index.php" method="post">
                <div class="mb-md-5 mt-md-4 pb-5">
                  <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
                  <p class="text-dark-50 mb-5">Please enter your email and password!</p>
                  <div class="form-outline form-white mb-4">
                    <input type="text" id="email" class="form-control form-control-lg" name="username" />
                    <label class="form-label" for="email">Email</label>
                  </div>
                  <div class="form-outline form-white mb-4">
                    <input type="password" id="password" class="form-control form-control-lg" name="password" />
                    <label class="form-label" for="password">Password</label>
                  </div>
                  <button class="btn btn-dark btn-lg px-5" type="submit">Login</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();

if(!isset($_SESSION['user'])){
    header("Location:index.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">

<h2>Welcome <?= $_SESSION['user']; ?></h2>

<a href="students.php"
   class="btn btn-success">
Students
</a>

<a href="attendance.php"
   class="btn btn-primary">
Attendance
</a>

<a href="attendance_sheet.php"
   class="btn btn-warning">
Attendance Sheet
</a>

<a href="logout.php"
   class="btn btn-danger">
Logout
</a>

</body>
</html>
<?php
include 'db.php';

if(isset($_POST['save'])){

    $student_no = $_POST['student_no'];
    $fullname = $_POST['fullname'];

    $stmt = $conn->prepare(
        "INSERT INTO students(student_no,fullname)
        VALUES(?,?)"
    );

    $stmt->bind_param(
        "ss",
        $student_no,
        $fullname
    );

    $stmt->execute();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Students</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h2>Students</h2>

<form method="POST">

<input type="text"
       name="student_no"
       placeholder="Student Number"
       class="form-control mb-2"
       required>

<input type="text"
       name="fullname"
       placeholder="Full Name"
       class="form-control mb-2"
       required>

<button name="save"
        class="btn btn-success">
Add Student
</button>

</form>

<hr>

<table class="table table-bordered">

<tr>
<th>ID</th>
<th>Student No</th>
<th>Name</th>
</tr>

<?php

$result = $conn->query(
"SELECT * FROM students ORDER BY fullname"
);

while($row=$result->fetch_assoc()){
?>

<tr>
<td><?= $row['id']; ?></td>
<td><?= $row['student_no']; ?></td>
<td><?= $row['fullname']; ?></td>
</tr>

<?php } ?>

</table>

</body>
</html>
<?php
include 'db.php';

if(isset($_POST['save'])){

    $date = date('Y-m-d');

    foreach($_POST['status'] as $id=>$status){

        $stmt = $conn->prepare(
        "INSERT INTO attendance
        (student_id,attendance_date,status)
        VALUES(?,?,?)"
        );

        $stmt->bind_param(
            "iss",
            $id,
            $date,
            $status
        );

        $stmt->execute();
    }

    echo "<script>alert('Attendance Saved');</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<h2>Mark Attendance</h2>

<form method="POST">

<table class="table table-bordered">

<tr>
<th>Name</th>
<th>Status</th>
</tr>

<?php

$students = $conn->query(
"SELECT * FROM students"
);

while($row=$students->fetch_assoc()){
?>

<tr>

<td><?= $row['fullname']; ?></td>

<td>

<select
name="status[<?= $row['id']; ?>]"
class="form-select">

<option value="Present">
Present
</option>

<option value="Absent">
Absent
</option>

<option value="Late">
Late
</option>

</select>

</td>

</tr>

<?php } ?>

</table>

<button
name="save"
class="btn btn-primary">
Save Attendance
</button>

</form>

</body>
</html>
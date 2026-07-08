<?php
include 'db.php';

$result = $conn->query(
"SELECT * FROM students ORDER BY fullname"
);
?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance Sheet</title>

<style>

table{
width:100%;
border-collapse:collapse;
}

th,td{
border:1px solid black;
padding:8px;
}

</style>
</head>

<body>

<h2 align="center">
Attendance Sheet
</h2>

<p>
Date:
<?= date('F d, Y'); ?>
</p>

<table>

<tr>
<th>#</th>
<th>Student No</th>
<th>Name</th>
<th>Present</th>
<th>Absent</th>
<th>Late</th>
<th>Remarks</th>
</tr>

<?php

$i=1;

while($row=$result->fetch_assoc()){
?>

<tr>
<td><?= $i++; ?></td>
<td><?= $row['student_no']; ?></td>
<td><?= $row['fullname']; ?></td>
<td></td>
<td></td>
<td></td>
<td></td>
</tr>

<?php } ?>

</table>

</body>
</html>
<?php
session_start();
include 'db.php';

/* =========================
   SAFE SESSION INIT
========================= */
$_SESSION['user'] = $_SESSION['user'] ?? null;
$_SESSION['role'] = $_SESSION['role'] ?? '';

/* =========================
   SIGN UP
========================= */
if(isset($_POST['signup'])){

    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = "user";

    $check = $conn->prepare("SELECT id FROM users WHERE username=?");
    $check->bind_param("s",$username);
    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){
        $signup_error = "Username already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO users(username,password,role) VALUES(?,?,?)");
        $stmt->bind_param("sss",$username,$password,$role);
        $stmt->execute();

        $signup_success = "Account created!";
    }
}

/* =========================
   LOGIN
========================= */
if(isset($_POST['login'])){

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s",$username);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();

        if(password_verify($password,$user['password'])){
            $_SESSION['user'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'user';

            header("Location:index.php");
            exit();
        }
    }

    $error = "Invalid login!";
}

/* =========================
   LOGOUT
========================= */
if(isset($_GET['logout'])){
    session_destroy();
    header("Location:index.php");
    exit();
}

/* =========================
   ADD STUDENT
========================= */
if(isset($_POST['add_student']) && $_SESSION['user']){

    $stmt = $conn->prepare("INSERT INTO students(student_no,fullname) VALUES(?,?)");
    $stmt->bind_param("ss",$_POST['student_no'],$_POST['fullname']);
    $stmt->execute();
}

/* =========================
   DELETE STUDENT
========================= */
if(isset($_GET['delete_student']) && $_SESSION['user']){

    $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
    $stmt->bind_param("i",$_GET['delete_student']);
    $stmt->execute();
}

/* =========================
   SAVE ATTENDANCE
========================= */
if(isset($_POST['save_attendance']) && $_SESSION['user']){

    $date = date('Y-m-d');

    if(isset($_POST['status']) && is_array($_POST['status'])){

        foreach($_POST['status'] as $id=>$status){

            $check = $conn->prepare("
                SELECT id FROM attendance
                WHERE student_id=? AND attendance_date=?
            ");
            $check->bind_param("is",$id,$date);
            $check->execute();
            $check->store_result();

            if($check->num_rows == 0){

                $stmt = $conn->prepare("
                    INSERT INTO attendance(student_id,attendance_date,status)
                    VALUES(?,?,?)
                ");

                $stmt->bind_param("iss",$id,$date,$status);
                $stmt->execute();
            }
        }
    }

    $msg = "Attendance Saved!";
}

/* =========================
   ADMIN DELETE USER
========================= */
if(isset($_GET['delete_user']) && $_SESSION['role'] === 'admin'){

    $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->bind_param("i",$_GET['delete_user']);
    $stmt->execute();
}

/* =========================
   ADMIN CREATE USER
========================= */
if(isset($_POST['create_user']) && $_SESSION['role'] === 'admin'){

    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];

    $stmt = $conn->prepare("
        INSERT INTO users(username,password,role)
        VALUES(?,?,?)
    ");

    $stmt->bind_param("sss",$username,$password,$role);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Attendance System</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-4">

<?php if(!isset($_SESSION['user']) || $_SESSION['user'] === null): ?>

<!-- LOGIN -->
<div class="card p-4 mx-auto" style="max-width:420px;">

<?php if(isset($_GET['signup'])): ?>

<h3>Sign Up</h3>

<?php if(isset($signup_error)) echo "<div class='alert alert-danger'>$signup_error</div>"; ?>
<?php if(isset($signup_success)) echo "<div class='alert alert-success'>$signup_success</div>"; ?>

<form method="POST">
<input type="text" name="username" class="form-control mb-2" required>
<input type="password" name="password" class="form-control mb-2" required>
<button class="btn btn-success w-100" name="signup">Create</button>
</form>

<a href="index.php" class="btn btn-secondary w-100 mt-2">Back</a>

<?php else: ?>

<h3>Login</h3>

<?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="POST">
<input type="text" name="username" class="form-control mb-2" required>
<input type="password" name="password" class="form-control mb-2" required>
<button class="btn btn-primary w-100" name="login">Login</button>
</form>

<a href="?signup=1" class="btn btn-success w-100 mt-2">Sign Up</a>

<?php endif; ?>

</div>

<?php else: ?>

<!-- DASHBOARD -->
<h3>
Welcome <?= htmlspecialchars($_SESSION['user']) ?>
(<?= $_SESSION['role'] ?? 'user' ?>)
</h3>

<a href="?logout=1" class="btn btn-danger btn-sm">Logout</a>

<hr>

<?php if(isset($msg)) echo "<div class='alert alert-success'>$msg</div>"; ?>

<!-- STUDENTS -->
<h4>Students</h4>

<form method="POST" class="row g-2">
<div class="col-md-4">
<input type="text" name="student_no" class="form-control" placeholder="Student No" required>
</div>

<div class="col-md-6">
<input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
</div>

<div class="col-md-2">
<button class="btn btn-success w-100" name="add_student">Add</button>
</div>
</form>

<br>

<table class="table table-bordered">
<tr>
<th>#</th>
<th>No</th>
<th>Name</th>
<th>Action</th>
</tr>

<?php
$res = $conn->query("SELECT * FROM students ORDER BY fullname");
$i=1;

while($row=$res->fetch_assoc()):
?>

<tr>
<td><?= $i++ ?></td>
<td><?= $row['student_no'] ?></td>
<td><?= $row['fullname'] ?></td>
<td>
<a href="?delete_student=<?= $row['id'] ?>" class="btn btn-danger btn-sm">Delete</a>
</td>
</tr>

<?php endwhile; ?>
</table>

<hr>

<!-- ATTENDANCE -->
<h4>Attendance</h4>

<form method="POST">

<table class="table table-bordered">
<tr>
<th>Name</th>
<th>Status</th>
</tr>

<?php
$students = $conn->query("SELECT * FROM students");

while($s=$students->fetch_assoc()):
?>

<tr>
<td><?= $s['fullname'] ?></td>
<td>
<select name="status[<?= $s['id'] ?>]" class="form-select">
<option>Present</option>
<option>Absent</option>
<option>Late</option>
</select>
</td>
</tr>

<?php endwhile; ?>

</table>

<button class="btn btn-primary" name="save_attendance">Save</button>

</form>

<hr>

<!-- ADMIN PANEL -->
<?php if(($_SESSION['role'] ?? '') === 'admin'): ?>

<h4>Admin Panel</h4>

<form method="POST" class="row g-2">
<div class="col">
<input type="text" name="username" class="form-control" placeholder="Username">
</div>

<div class="col">
<input type="password" name="password" class="form-control" placeholder="Password">
</div>

<div class="col">
<select name="role" class="form-select">
<option value="user">User</option>
<option value="admin">Admin</option>
</select>
</div>

<div class="col">
<button class="btn btn-success" name="create_user">Add User</button>
</div>
</form>

<?php endif; ?>

<?php endif; ?>

</body>
</html>
<?php
include 'db.php';

$message = "";

if(isset($_POST['signup'])){

    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare(
        "SELECT id FROM users WHERE username=?"
    );

    $check->bind_param("s",$username);
    $check->execute();
    $check->store_result();

    if($check->num_rows > 0){

        $message = "
        <div class='alert alert-danger'>
            Username already exists!
        </div>";

    }else{

        $stmt = $conn->prepare(
            "INSERT INTO users(username,password)
            VALUES(?,?)"
        );

        $stmt->bind_param("ss",$username,$password);

        if($stmt->execute()){

            echo "
            <script>
                alert('Account Created Successfully!');
                window.location='index.php';
            </script>
            ";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Sign Up</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="container mt-5">

<h2>Create Account</h2>

<?= $message ?>

<form method="POST">

<input type="text"
       name="username"
       class="form-control mb-3"
       placeholder="Username"
       required>

<input type="password"
       name="password"
       class="form-control mb-3"
       placeholder="Password"
       required>

<button type="submit"
        name="signup"
        class="btn btn-success">
Create Account
</button>

<a href="index.php"
   class="btn btn-secondary">
Back
</a>

</form>

</body>
</html>
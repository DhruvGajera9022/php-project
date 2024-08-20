<?php

require_once '../database/config.php';

session_start();

if (isset($_SESSION['id'])) {
  header('Location: ../admin/dashboard/dashboard.php');
  exit;
}

if (isset($_POST['register'])) {

  $fname = trim($_POST['fname']);
  $lname = trim($_POST['lname']);
  $email = trim($_POST['email']);
  $password = $_POST['password'];
  $cpassword = $_POST['cpassword'];

  $errors = [];

  if (empty($fname)) {
    $errors['fname'] = "Enter first name";
  } elseif (!ctype_alpha($fname)) {
    $errors['fname'] = "Enter a valid name";
  }

  if (empty($lname)) {
    $errors['lname'] = "Enter last name";
  } elseif (!ctype_alpha($lname)) {
    $errors['lname'] = "Enter a valid name";
  }

  if (empty($email)) {
    $errors['email'] = "Enter email";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Enter a valid email";
  } else {
    $stmt = $conn->prepare("SELECT email FROM tbluser WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $errors['email'] = "Email already registered";
    }
    $stmt->close();
  }

  if (empty($password)) {
    $errors['password'] = "Enter Password";
  }

  if (empty($cpassword)) {
    $errors['cpassword'] = "Enter Confirm Password";
  }

  if ($password != $cpassword) {
    $errors['compare'] = "Password and Confirm Password should be the same";
  }

  if (empty($errors)) {
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO tbluser (fname, lname, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $fname, $lname, $email, $passwordHash);

    if ($stmt->execute()) {
      header("Location: login.php");
      exit;
    } else {
      $errors['db_error'] = "Database error: Failed to register";
    }
    $stmt->close();
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registration Page</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <!-- <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css"> -->
  <!-- icheck bootstrap -->
  <!-- <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css"> -->
  <!-- Theme style -->
  <link rel="stylesheet" href="../assets/css/adminlte.min.css">
  <style>
    .error {
      color: red;
    }
  </style>
</head>

<body class="hold-transition register-page">
  <div class="register-box">
    <div class="register-logo">
      <a href="../../index2.html"><b>Registration</b></a>
    </div>

    <div class="card">
      <div class="card-body register-card-body">
        <p class="login-box-msg">Register a new membership</p>

        <form method="post" id="registerForm">

          <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger">
              <?php foreach ($errors as $error) : ?>
                <p><?php echo htmlspecialchars($error); ?></p>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <input type="text" class="form-control" name="fname" id="fname" placeholder="First name" value="<?php echo isset($fname) ? htmlspecialchars($fname) : ''; ?>"><br>
          <input type="text" class="form-control" name="lname" id="lname" placeholder="Last name" value="<?php echo isset($lname) ? htmlspecialchars($lname) : ''; ?>"><br>
          <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>"><br>
          <input type="password" class="form-control" name="password" id="password" placeholder="Password"><br>
          <input type="password" class="form-control" name="cpassword" id="cpassword" placeholder="Retype password"><br>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="agreeTerms" name="terms" value="agree">
                <label for="agreeTerms">
                  I agree to the <a href="all-policy.php">terms</a>
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" name="register" class="btn btn-primary btn-block">Register</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        <div class="social-auth-links text-center">
          <p>- OR -</p>
          <a href="#" class="btn btn-block btn-primary">
            <i class="fab fa-facebook mr-2"></i>
            Sign up using Facebook
          </a>
          <a href="#" class="btn btn-block btn-danger">
            <i class="fab fa-google-plus mr-2"></i>
            Sign up using Google+
          </a>
        </div>

        <a href="login.php" class="text-center">I already have a membership</a>
      </div>
      <!-- /.form-box -->
    </div><!-- /.card -->
  </div>
  <!-- /.register-box -->

  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <!-- <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script> -->
  <!-- AdminLTE App -->
  <!-- <script src="../../dist/js/adminlte.min.js"></script> -->
  <!-- jQuery Validation -->
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
  <!-- For Validation -->
  <script src="../assets/javascript/validation.js"></script>
</body>

</html>
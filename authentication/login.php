<?php
require_once '../database/config.php';

if (isset($_POST['signin'])) {
  $email = $_POST['email'];
  $password = $_POST['password'];

  if (empty($email)) {
    $errors['email'] = "Enter email";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = "Enter a valid email";
  }

  if (empty($password)) {
    $errors['password'] = "Enter Password";
  }

  if (empty($errors)) {
    $sqlSelect = $conn->prepare("SELECT * FROM tbluser WHERE email = '$email'");
    $sqlSelect->execute();
    $result = $sqlSelect->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
      header('Location: ../php/admin.php');
      exit();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <!-- <link rel="stylesheet" href="../../plugins/fontawesome-free/css/all.min.css"> -->
  <!-- icheck bootstrap -->
  <!-- <link rel="stylesheet" href="../../plugins/icheck-bootstrap/icheck-bootstrap.min.css"> -->
  <!-- Theme style -->
  <link rel="stylesheet" href="../css/adminlte.min.css">
  <style>
    .error {
      color: red;
    }
  </style>
</head>

<body class="hold-transition login-page">
  <div class="login-box">
    <div class="login-logo">
      <a href="../../index2.html"><b>Login</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="card">
      <div class="card-body login-card-body">
        <p class="login-box-msg">Sign in to start your session</p>
        <?php if (isset($error)) : ?>
          <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="post" id="loginForm">

          <?php if (!empty($errors)) : ?>
            <div class="alert alert-danger">
              <?php foreach ($errors as $error) : ?>
                <p><?php echo $error; ?></p>
              <?php endforeach; ?>
            </div>
          <?php endif; ?>

          <input type="email" class="form-control" name="email" placeholder="Email"><br>
          <input type="password" class="form-control" name="password" placeholder="Password"><br>
          <div class="row">
            <div class="col-8">
              <div class="icheck-primary">
                <input type="checkbox" id="remember">
                <label for="remember">
                  Remember Me
                </label>
              </div>
            </div>
            <!-- /.col -->
            <div class="col-4">
              <button type="submit" name="signin" class="btn btn-primary btn-block">Sign In</button>
            </div>
            <!-- /.col -->
          </div>
        </form>

        <div class="social-auth-links text-center mb-3">
          <p>- OR -</p>
          <a href="#" class="btn btn-block btn-primary">
            <i class="fab fa-facebook mr-2"></i> Sign in using Facebook
          </a>
          <a href="#" class="btn btn-block btn-danger">
            <i class="fab fa-google-plus mr-2"></i> Sign in using Google+
          </a>
        </div>
        <!-- /.social-auth-links -->

        <p class="mb-1">
          <a href="forgot-password.php">I forgot my password</a>
        </p>
        <p class="mb-0">
          <a href="register.php" class="text-center">Register a new membership</a>
        </p>
      </div>
      <!-- /.login-card-body -->
    </div>
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <!-- <script src="../../plugins/bootstrap/js/bootstrap.bundle.min.js"></script> -->
  <!-- AdminLTE App -->
  <!-- <script src="../../dist/js/adminlte.min.js"></script> -->
  <!-- jQuery Validation -->
  <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
  <!-- For Validation -->
  <script src="../javascript/validation.js"></script>
</body>

</html>
<?php

require_once '../database/config.php';

session_start();

if (isset($_SESSION['id'])) {
    header('Location: ../admin/dashboard/dashboard.php');
    exit;
}
$query = "SELECT * FROM tblpolicy";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$res = $stmt->get_result();

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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            margin-top: 400px;
            margin-bottom: 400px;
            font-family: 'Source Sans Pro', sans-serif;
        }

        .register-box {
            width: 400px;
            padding: 20px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .register-logo a {
            font-size: 1.5em;
            color: #333;
            font-weight: bold;
        }

        .policy-container {
            background-color: #f9f9f9;
            padding: 30px;
            margin-top: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .policy-container h4 {
            text-align: center;
            font-size: 1.5em;
            margin-bottom: 15px;
            color: #444;
        }

        .policy-container p {
            font-size: 1em;
            line-height: 1.6;
            color: #555;
            text-align: justify;
        }

        .error {
            color: red;
        }
    </style>
</head>

<body>
    <div class="register-box">
        <div class="register-logo">
            <a href="../../index2.html"><b>Policy</b></a>
        </div>

        <?php while ($data = $res->fetch_assoc()) { ?>
        <div class="policy-container">
            <h4><?php echo $data['name'];?></h4>
            <p><?php echo $data['description'];?></p>
        </div>
        <?php } ?>
    </div>

    <!-- jQuery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <!-- Bootstrap 4 -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery Validation -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
    <!-- For Validation -->
    <script src="../assets/javascript/validation.js"></script>
</body>

</html>
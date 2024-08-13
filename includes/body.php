<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $title; ?></title>

    <?php include_once 'head.php'; ?>

    <style>
        .error {
            color: red;
            font-weight: 100;
        }
    </style>

</head>

<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        <!-- Including Header -> header.php -->
        <?php include_once 'header.php'; ?>

        <!-- Preloader -->
        <!-- <div class="preloader flex-column justify-content-center align-items-center">
            <img class="<?php echo $title == 'Dashboard' ? 'animation__shake' : ''; ?>" src="../assets/img/AdminLTELogo.png" alt="AdminLTELogo" height="60" width="60">
        </div> -->

        <!-- Including Left Main Sidebar Container -->
        <?php include_once 'slider.php'; ?>

    </div> <!-- Closing the wrapper div -->

</body>

</html>
<?php
require_once '../database/config.php';
session_start();

if (!isset($_SESSION['id'])) {
    header("Location: ../authentication/login.php");
    exit;
}

$id = $_SESSION['id'];

$sqlSelect = "SELECT * FROM tbluser WHERE id = ?";
$stmt = $conn->prepare($sqlSelect);
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$data = $res->fetch_assoc();

$fetchHobby1 = $data['hobby'];
$fetchHobby = explode(", ",$fetchHobby1);

$image = $data['image'];
$fname = $data['fname'];

$errors = [];

if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $number = trim($_POST['number']);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $hobby = $_POST['hobby'];

    $strHobby = implode(", ", $hobby);

    $new_image = $_FILES['image']['name'];
    $temp_name = $_FILES['image']['tmp_name'];
    $old_image = $_POST['image_old'];
    $folder = "../assets/img/userimage/" . basename($new_image);

    // Validate inputs
    if (empty($name)) {
        $errors['name'] = "Enter name";
    } elseif (!ctype_alpha(str_replace(' ', '', $name))) {
        $errors['name'] = "Enter a valid name";
    }

    if (empty($email)) {
        $errors['email'] = "Enter email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Enter a valid email";
    }

    if (empty($number)) {
        $errors['number'] = "Enter mobile number";
    } elseif (!is_numeric($number) || strlen($number) != 10) {
        $errors['number'] = "Enter a valid 10-digit phone number";
    }

    if (empty($gender)) {
        $errors['gender'] = "Please select gender";
    }

    if (empty($dob)) {
        $errors['dob'] = "Please select date of birth";
    }

    if (empty($hobby)) {
        $errors['hobby'] = "Please select hobby";
    }

    // Image upload handling
    $update_filename = $old_image;
    if ($new_image != '') {
        if (in_array($_FILES['image']['type'], ['image/jpeg', 'image/png', 'image/gif'])) {
            $update_filename = $new_image;
        } else {
            $errors['image'] = "Invalid image format. Only JPEG, PNG, and GIF are allowed.";
        }
    }

    // If no errors, proceed with the update
    if (empty($errors)) {
        $sqlUpdate = "UPDATE tbluser SET fname = ?, email = ?, number = ?, gender = ?, dob = ?, hobby = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($sqlUpdate);
        $stmt->bind_param("sssssssi", $name, $email, $number, $gender, $dob, $strHobby, $update_filename, $id);

        if ($stmt->execute()) {
            if ($new_image != '') {
                move_uploaded_file($temp_name, $folder);
                if ($old_image != '' && file_exists("../assets/img/userimage/" . $old_image)) {
                    unlink("../assets/img/userimage/" . $old_image);
                }
            }
            header("Location: profile.php");
            exit;
        } else {
            $errors['update'] = "Error updating record.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Profile</title>

    <?php include_once '../includes/head.php'; ?>

</head>

<body class="hold-transition sidebar-mini">
    <?php include_once '../includes/header.php'; ?>
    <?php include_once '../includes/slider.php'; ?>

    <div class="wrapper">
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1>Profile</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item active">User Profile</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </section>

            <section class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card card-primary card-outline">
                                <div class="card-body box-profile">
                                    <div class="text-center">
                                        <img class="profile-user-img img-fluid img-circle" src="../assets/img/userimage/<?php echo htmlspecialchars($data['image']); ?>" alt="User profile picture">
                                    </div>
                                    <h3 class="profile-username text-center"><?php echo htmlspecialchars($data['fname']); ?></h3>
                                    <p class="text-muted text-center">Software Engineer</p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-9">
                            <div class="card">
                                <div class="card-header p-2">
                                    <ul class="nav nav-pills">
                                        <li class="nav-item"><a class="nav-link active" href="#activity" data-toggle="tab">Profile</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#settings" data-toggle="tab">Change</a></li>
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        <div class="active tab-pane" id="activity">
                                            <form class="form-horizontal">
                                                <div class="form-group row">
                                                    <label for="inputName" class="col-sm-2 col-form-label">Name</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputName" value="<?php echo htmlspecialchars($data['fname']); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                                    <div class="col-sm-10">
                                                        <input type="email" class="form-control" id="inputEmail" value="<?php echo htmlspecialchars($data['email']); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputNumber" class="col-sm-2 col-form-label">Number</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputNumber" value="<?php echo htmlspecialchars($data['number']); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputGender" class="col-sm-2 col-form-label">Gender</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputGender" value="<?php echo htmlspecialchars($data['gender']); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputDob" class="col-sm-2 col-form-label">Date of Birth</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputDob" value="<?php echo htmlspecialchars($data['dob']); ?>" disabled>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputHobbies" class="col-sm-2 col-form-label">Hobbies</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputHobbies" value="<?php echo htmlspecialchars($data['hobby']); ?>" disabled>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="tab-pane" id="settings">
                                            <form class="form-horizontal" method="post" enctype="multipart/form-data" id="formProfile">
                                                <div class="form-group row">
                                                    <label for="inputImage" class="col-sm-2 col-form-label">Profile Image</label>
                                                    <div class="col-sm-10">
                                                        <input type="file" class="form-control" name="image" id="inputImage">
                                                        <input type="hidden" name="image_old" value="<?php echo $data['image']; ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputName" class="col-sm-2 col-form-label">Full Name</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputName" name="name" placeholder="Name" value="<?php echo htmlspecialchars($data['fname']); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputEmail" class="col-sm-2 col-form-label">Email</label>
                                                    <div class="col-sm-10">
                                                        <input type="email" class="form-control" id="inputEmail" name="email" placeholder="Email" value="<?php echo htmlspecialchars($data['email']); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputNumber" class="col-sm-2 col-form-label">Number</label>
                                                    <div class="col-sm-10">
                                                        <input type="text" class="form-control" id="inputNumber" name="number" placeholder="Number" value="<?php echo htmlspecialchars($data['number']); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputGender" class="col-sm-2 col-form-label">Gender</label>
                                                    <div class="col-sm-10">
                                                        <label for="male">
                                                            <input type="radio" name="gender" id="male" value="Male" <?php echo $data['gender'] == "Male" ? "checked" : ""; ?>> Male
                                                        </label>
                                                        <label for="female">
                                                            <input type="radio" name="gender" id="female" value="Female" <?php echo $data['gender'] == "Female" ? "checked" : ""; ?>> Female
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputDob" class="col-sm-2 col-form-label">Date of Birth</label>
                                                    <div class="col-sm-10">
                                                        <input type="date" class="form-control" id="inputDob" name="dob" value="<?php echo htmlspecialchars($data['dob']); ?>">
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <label for="inputHobbies" class="col-sm-2 col-form-label">Hobbies</label>
                                                    <div class="col-sm-10">
                                                        <label for="singing"><input type="checkbox" name="hobby[]" value="Singing" 
                                                        <?php 
                                                            if(in_array("Singing", $fetchHobby)){
                                                                echo "checked";
                                                            }
                                                        ?>
                                                        id="singing"> Singing</label>
                                                        <label for="dancing"><input type="checkbox" name="hobby[]" value="Dancing" 
                                                        <?php 
                                                            if(in_array("Dancing", $fetchHobby)){
                                                                echo "checked";
                                                            }
                                                        ?>
                                                        id="dancing"> Dancing</label>
                                                        <label for="writing"><input type="checkbox" name="hobby[]" value="Writing" 
                                                        <?php 
                                                            if(in_array("Writing", $fetchHobby)){
                                                                echo "checked";
                                                            }
                                                        ?>
                                                        id="writing"> Writing</label>
                                                        <label for="reading"><input type="checkbox" name="hobby[]" value="Reading"
                                                        <?php 
                                                            if(in_array("Reading", $fetchHobby)){
                                                                echo "checked";
                                                            }
                                                        ?>
                                                        id="reading"> Reading</label>
                                                        <label for="swimming"><input type="checkbox" name="hobby[]" value="Swimmings" 
                                                        <?php 
                                                            if(in_array("Swimmings", $fetchHobby)){
                                                                echo "checked";
                                                            }
                                                        ?>
                                                        id="swimming"> Swimming</label>
                                                        <label for="travelling"><input type="checkbox" name="hobby[]" value="Travelling" 
                                                        <?php 
                                                            if(in_array("Travelling", $fetchHobby)){
                                                                echo "checked";
                                                            }
                                                        ?>
                                                        id="travelling"> Travelling</label>
                                                    </div>
                                                </div>
                                                <div class="form-group row">
                                                    <div class="offset-sm-2 col-sm-10">
                                                        <button type="submit" class="btn btn-primary" name="submit" id="submit">Submit</button>
                                                    </div>
                                                </div>
                                            </form>

                                            <?php
                                            if (!empty($errors)) {
                                                echo '<div class="alert alert-danger">';
                                                foreach ($errors as $error) {
                                                    echo '<p>' . htmlspecialchars($error) . '</p>';
                                                }
                                                echo '</div>';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <?php include_once '../includes/footer.php'; ?>
        <aside class="control-sidebar control-sidebar-dark"></aside>
    </div>

    <?php include_once '../includes/scripts.php'; ?>
</body>

</html>
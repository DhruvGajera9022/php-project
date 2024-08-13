<?php
require_once '../../database/config.php';

// Start the session and regenerate session ID to prevent session fixation
session_start();

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['id'])) {
    header("Location: ../../authentication/login.php");
    exit;
}

// Retrieve the logged-in user's ID from the session
$id = $_SESSION['id'];

// Prepare and execute the SQL statement to fetch the user's data
$sqlSelect = "SELECT * FROM tbluser WHERE id = ?";
$stmt = $conn->prepare($sqlSelect);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("i", $id);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$res = $stmt->get_result();
$data = $res->fetch_assoc();

// Sanitize the user's data before displaying it
$image = htmlspecialchars($data['image']);
$fname = htmlspecialchars($data['fname']);

$errors = [];  // Initialize errors array


if (isset($_POST['add'])) {

    // Fetch form inputs
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];
    $number = $_POST['number'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $hobby = $_POST['hobby'];
    $role = $_POST['role'];

    // Convert hobbies array to string
    $strHobby = implode(", ", $hobby);

    // Validate inputs
    if (empty($fname)) {
        $errors['fname'] = "Enter first name";
    } elseif (!ctype_alpha(str_replace(' ', '', $fname))) {
        $errors['fname'] = "Enter a valid name";
    }

    if (empty($lname)) {
        $errors['lname'] = "Enter last name";
    } elseif (!ctype_alpha(str_replace(' ', '', $lname))) {
        $errors['lname'] = "Enter a valid name";
    }

    if (empty($email)) {
        $errors['email'] = "Enter email";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Enter a valid email";
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
        $errors['hobby'] = "Please select at least one hobby";
    }

    if (empty($role)) {
        $errors['role'] = "Please select role";
    }

    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $newDob = date("d-m-Y", strtotime($dob));

    // If no errors, proceed with insertion
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO tbluser(fname, lname, email, number, password, gender, dob, hobby, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("sssssssss", $fname, $lname, $email, $number, $passwordHash, $gender, $newDob, $strHobby, $role);

            if ($stmt->execute()) {
                header("Location: users.php");
                exit;
            } else {
                $errors['db_error'] = "Database error: Failed to register";
            }
            $stmt->close();
        } else {
            $errors['db_error'] = "Database error: Failed to prepare statement";
        }
    }
}

// Define title
$title = "Users";
?>

<?php include_once '../../includes/body.php'; ?>

<div class="content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo $title; ?></h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title">Add</h3>
                                </div>
                                <form method="post" id="formAddUser">
                                    <div class="card-body">
                                        <div class="form-group row">
                                            <label for="fname" class="col-sm-2 col-form-label">First Name (*)</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="fname" name="fname" placeholder="Enter First Name">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="lname" class="col-sm-2 col-form-label">Last Name (*)</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="lname" name="lname" placeholder="Enter Last Name">
                                                <?php if (isset($errors['lname'])): ?>
                                                    <small class="text-danger"><?php echo $errors['lname']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email" class="col-sm-2 col-form-label">Email (*)</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="password" class="col-sm-2 col-form-label">Password (*)</label>
                                            <div class="col-sm-10">
                                                <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="cpassword" class="col-sm-2 col-form-label">Confirm Password (*)</label>
                                            <div class="col-sm-10">
                                                <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="Enter Confirm Password">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="number" class="col-sm-2 col-form-label">Phone Number (*)</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="number" name="number" placeholder="Enter Number">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="gender" class="col-sm-2 col-form-label">Gender (*)</label>
                                            <div class="col-sm-10">
                                                <label for="male">
                                                    <input type="radio" name="gender" id="male" value="Male" checked> Male
                                                </label>
                                                <label for="female">
                                                    <input type="radio" name="gender" id="female" value="Female"> Female
                                                </label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="dob" class="col-sm-2 col-form-label">Date of Birth</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control" id="dob" name="dob">
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="hobby" class="col-sm-2 col-form-label">Hobbies</label>
                                            <div class="col-sm-10">
                                                <label for="singing"><input type="checkbox" name="hobby[]" value="Singing" id="singing"> Singing</label>
                                                <label for="dancing"><input type="checkbox" name="hobby[]" value="Dancing" id="dancing"> Dancing</label>
                                                <label for="writing"><input type="checkbox" name="hobby[]" value="Writing" id="writing"> Writing</label>
                                                <label for="reading"><input type="checkbox" name="hobby[]" value="Reading" id="reading"> Reading</label>
                                                <label for="swimming"><input type="checkbox" name="hobby[]" value="Swimming" id="swimming"> Swimming</label>
                                                <label for="travelling"><input type="checkbox" name="hobby[]" value="Travelling" id="travelling"> Travelling</label>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="role" class="col-sm-2 col-form-label">Role (*)</label>
                                            <div class="col-sm-10">
                                                <select name="role" id="role" class="form-control">
                                                    <option value="">Select Role</option>

                                                    <?php

                                                    $sqlSelectRole = "SELECT name FROM tblrole";
                                                    $resultSelectRole = mysqli_query($conn, $sqlSelectRole);
                                                    foreach ($resultSelectRole as $result) {
                                                        echo "<option value='" . $result['name'] . "'>" . $result['name'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="add" class="btn btn-primary">Submit</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once '../../includes/footer.php'; ?>
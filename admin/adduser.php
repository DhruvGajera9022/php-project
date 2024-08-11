<?php
require_once '../database/config.php';
session_start();
session_regenerate_id(true);

// Redirect if not logged in
if (!isset($_SESSION['id'])) {
    header("Location: ../authentication/login.php");
    exit;
}

// Get Session Id
$id = $_SESSION['id'];
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
$stmt->close(); // Close the statement

// Get image and name for slider
$image = htmlspecialchars($data['image']);
$fname = htmlspecialchars($data['fname']);

$errors = [];  // Initialize errors array

// Initialize form variables
$fname = $lname = $email = $number = $gender = $dob = $hobby = $role = '';

if (isset($_POST['add'])) {

    // Fetch form inputs
    $fname = htmlspecialchars($_POST['fname']);
    $lname = htmlspecialchars($_POST['lname']);
    $email = htmlspecialchars($_POST['email']);
    $number = htmlspecialchars($_POST['number']);
    $gender = htmlspecialchars($_POST['gender']);
    $dob = htmlspecialchars($_POST['dob']);
    $hobby = isset($_POST['hobby']) ? $_POST['hobby'] : [];
    $role = htmlspecialchars($_POST['role']);

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

    // If no errors, proceed with insertion
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO tbluser(fname, lname, email, number, gender, dob, hobby, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt) {
            $stmt->bind_param("ssssssss", $fname, $lname, $email, $number, $gender, $dob, $strHobby, $role);

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
$title = "All Users";
?>

<?php include_once '../includes/body.php'; ?>

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
                                            <label for="fname" class="col-sm-2 col-form-label">First Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="fname" name="fname" placeholder="Enter First Name" value="<?php echo htmlspecialchars($fname); ?>">
                                                <span class="text-danger"><?php echo $errors['fname'] ?? ''; ?></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="lname" class="col-sm-2 col-form-label">Last Name</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="lname" name="lname" placeholder="Enter Last Name" value="<?php echo htmlspecialchars($lname); ?>">
                                                <span class="text-danger"><?php echo $errors['lname'] ?? ''; ?></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="email" class="col-sm-2 col-form-label">Email</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" value="<?php echo htmlspecialchars($email); ?>">
                                                <span class="text-danger"><?php echo $errors['email'] ?? ''; ?></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="number" class="col-sm-2 col-form-label">Number</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="number" name="number" placeholder="Enter Number" value="<?php echo htmlspecialchars($number); ?>">
                                                <span class="text-danger"><?php echo $errors['number'] ?? ''; ?></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="gender" class="col-sm-2 col-form-label">Gender</label>
                                            <div class="col-sm-10">
                                                <label for="male">
                                                    <input type="radio" name="gender" id="male" value="Male" <?php echo ($gender === 'Male') ? 'checked' : ''; ?>> Male
                                                </label>
                                                <label for="female">
                                                    <input type="radio" name="gender" id="female" value="Female" <?php echo ($gender === 'Female') ? 'checked' : ''; ?>> Female
                                                </label>
                                                <span class="text-danger"><?php echo $errors['gender'] ?? ''; ?></span>
                                            </div>
                                        </div>
                                        <div class="form-group row">
                                            <label for="dob" class="col-sm-2 col-form-label">Date of Birth</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($dob); ?>">
                                                <span class="text-danger"><?php echo $errors['dob'] ?? ''; ?></span>
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
                                            <label for="role" class="col-sm-2 col-form-label">Role</label>
                                            <div class="col-sm-10">
                                                <select name="role" id="role" class="form-control">
                                                    <option value="">Select Role</option>
                                                    <option value="1">Admin</option>
                                                    <option value="2">User</option>
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

<?php include_once '../includes/footer.php'; ?>
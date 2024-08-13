<?php
require_once '../../database/config.php';
session_start();

// Redirect if not logged in
if (!isset($_SESSION['id'])) {
    header("Location: ../../authentication/login.php");
    exit;
}

session_regenerate_id(true); // Regenerate session ID for logged-in users

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

// Redirect if user not found
if (!$data) {
    header("Location: ../../authentication/logout.php");
    exit;
}

// Get image and name for slider
$image = htmlspecialchars($data['image']);
$fname = htmlspecialchars($data['fname']);

// Initialize $updid before using it
$updid = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($updid) {
    $stmt = $conn->prepare("SELECT * FROM tbluser WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $updid);
    $stmt->execute();
    $res = $stmt->get_result();

    $data = $res->fetch_assoc();
    $fetchHobby1 = $data['hobby'];
    $fetchHobby = explode(", ", $fetchHobby1);

    $fetchDob = $data['dob'];
    $fetchNewDob = date("Y-m-d", strtotime($fetchDob)); // Corrected date format for HTML input

    $stmt->close();
}

$errors = [];  // Initialize errors array

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit'])) {

    // Fetch form inputs
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $email = $_POST['email'];
    $number = $_POST['number'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $hobby = isset($_POST['hobby']) ? $_POST['hobby'] : [];
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

    $newDob = date("d-m-Y", strtotime($dob)); // Correct format for database

    // If no errors, proceed with the update
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE tbluser SET fname = ?, lname = ?, email = ?, number = ?, gender = ?, dob = ?, hobby = ?, role = ? WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("ssssssssi", $fname, $lname, $email, $number, $gender, $newDob, $strHobby, $role, $updid);

            if ($stmt->execute()) {
                header("Location: users.php");
                exit;
            } else {
                $errors['db_error'] = "Database error: Failed to update user";
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
                                    <h3 class="card-title">Edit</h3>
                                </div>

                                <form method="post" id="formAddUser">
                                    <div class="card-body">
                                        <!-- First Name -->
                                        <div class="form-group row">
                                            <label for="fname" class="col-sm-2 col-form-label">First Name (*)</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="fname" name="fname" placeholder="Enter First Name" value="<?php echo htmlspecialchars($data['fname']); ?>">
                                                <?php if (isset($errors['fname'])): ?>
                                                    <small class="text-danger"><?php echo $errors['fname']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Last Name -->
                                        <div class="form-group row">
                                            <label for="lname" class="col-sm-2 col-form-label">Last Name (*)</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="lname" name="lname" placeholder="Enter Last Name" value="<?php echo htmlspecialchars($data['lname']); ?>">
                                                <?php if (isset($errors['lname'])): ?>
                                                    <small class="text-danger"><?php echo $errors['lname']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Email -->
                                        <div class="form-group row">
                                            <label for="email" class="col-sm-2 col-form-label">Email (*)</label>
                                            <div class="col-sm-10">
                                                <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email" value="<?php echo htmlspecialchars($data['email']); ?>">
                                                <?php if (isset($errors['email'])): ?>
                                                    <small class="text-danger"><?php echo $errors['email']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Number -->
                                        <div class="form-group row">
                                            <label for="number" class="col-sm-2 col-form-label">Phone Number (*)</label>
                                            <div class="col-sm-10">
                                                <input type="text" class="form-control" id="number" name="number" placeholder="Enter Number" value="<?php echo htmlspecialchars($data['number']); ?>">
                                                <?php if (isset($errors['number'])): ?>
                                                    <small class="text-danger"><?php echo $errors['number']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Gender -->
                                        <div class="form-group row">
                                            <label for="gender" class="col-sm-2 col-form-label">Gender (*)</label>
                                            <div class="col-sm-10">
                                                <label for="male">
                                                    <input type="radio" name="gender" id="male" value="Male" <?php if ($data['gender'] == "Male") { ?> checked="true" <?php } ?>> Male
                                                </label>
                                                <label for="female">
                                                    <input type="radio" name="gender" id="female" value="Female" <?php if ($data['gender'] == "Female") { ?> checked="true" <?php } ?>> Female
                                                </label>
                                                <?php if (isset($errors['gender'])): ?>
                                                    <small class="text-danger"><?php echo $errors['gender']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Date of Birth -->
                                        <div class="form-group row">
                                            <label for="dob" class="col-sm-2 col-form-label">Date of Birth</label>
                                            <div class="col-sm-10">
                                                <input type="date" class="form-control" id="dob" name="dob" value="<?php echo $fetchNewDob; ?>">
                                                <?php if (isset($errors['dob'])): ?>
                                                    <small class="text-danger"><?php echo $errors['dob']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Hobbies -->
                                        <div class="form-group row">
                                            <label for="hobby" class="col-sm-2 col-form-label">Hobbies</label>
                                            <div class="col-sm-10">
                                                <label for="singing"><input type="checkbox" name="hobby[]" value="Singing" id="singing" <?php echo in_array("Singing", $fetchHobby) ? "checked" : ""; ?>> Singing</label>
                                                <label for="dancing"><input type="checkbox" name="hobby[]" value="Dancing" id="dancing" <?php echo in_array("Dancing", $fetchHobby) ? "checked" : ""; ?>> Dancing</label>
                                                <label for="writing"><input type="checkbox" name="hobby[]" value="Writing" id="writing" <?php echo in_array("Writing", $fetchHobby) ? "checked" : ""; ?>> Writing</label>
                                                <label for="reading"><input type="checkbox" name="hobby[]" value="Reading" id="reading" <?php echo in_array("Reading", $fetchHobby) ? "checked" : ""; ?>> Reading</label>
                                                <label for="swimming"><input type="checkbox" name="hobby[]" value="Swimming" id="swimming" <?php echo in_array("Swimming", $fetchHobby) ? "checked" : ""; ?>> Swimming</label>
                                                <label for="travelling"><input type="checkbox" name="hobby[]" value="Travelling" id="travelling" <?php echo in_array("Travelling", $fetchHobby) ? "checked" : ""; ?>> Travelling</label>
                                                <?php if (isset($errors['hobby'])): ?>
                                                    <small class="text-danger"><?php echo $errors['hobby']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Role -->
                                        <div class="form-group row">
                                            <label for="role" class="col-sm-2 col-form-label">Role (*)</label>
                                            <div class="col-sm-10">
                                                <select name="role" id="role" class="form-control">
                                                    <option value="">Select Role</option>
                                                    <?php

                                                    $sqlSelectRole = "SELECT name FROM tblrole";
                                                    $resultSelectRole = mysqli_query($conn, $sqlSelectRole);
                                                    foreach ($resultSelectRole as $result) {
                                                        $selectedRole = $data['role'];
                                                        $isSelected = ($result['name'] == $selectedRole) ? "selected" : "";
                                                        echo "<option value='" . $result['name'] . "' " . $isSelected . ">" . $result['name'] . "</option>";
                                                    }
                                                    ?>
                                                </select>
                                                <?php if (isset($errors['role'])): ?>
                                                    <small class="text-danger"><?php echo $errors['role']; ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <button type="submit" name="edit" class="btn btn-primary">Submit</button>
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
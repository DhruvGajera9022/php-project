<?php
// Include the database configuration file
require_once '../../database/config.php';

// Start the session and regenerate session ID to prevent session fixation
session_start();
session_regenerate_id(true);

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['id'])) {
    header("Location: ../../authentication/login.php");
    exit;
}

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
$stmt->close();

$image = htmlspecialchars($data['image']);
$fname = htmlspecialchars($data['fname']);

// Initialize an array to store role names
$existingRoles = [];

$queryRole = "SELECT name FROM tblrole";
$stmt = $conn->prepare($queryRole);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$resRoleName = $stmt->get_result();
while ($row = $resRoleName->fetch_assoc()) {
    $existingRoles[] = $row['name'];
}
$stmt->close();

// Initialize an array to store error messages
$error = [];

if (isset($_POST['add'])) {
    $fullname = trim($_POST['fullname']);
    $description = trim($_POST['description']);

    if (empty($fullname)) {
        $error['fullname'] = "Enter Full Name";
    }

    if (in_array($fullname, $existingRoles)) {
        $error['role_name'] = "Role Name already exists";
    }

    // If there are no errors, proceed to insert the role into the database
    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO tblrole (name, description) VALUES (?, ?)");
        if (!$stmt) {
            $error['db_error'] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("ss", $fullname, $description);
            if ($stmt->execute()) {
                header("Location: roles.php");
                exit;
            } else {
                $error['db_error'] = "Database error: Failed to register";
            }
            $stmt->close();
        }
    }
}

// Set the page title
$title = "Role";
?>

<?php include_once '../../includes/body.php'; ?>

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Role</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Main content -->
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
                                <!-- /.card-header -->

                                <!-- form start -->
                                <form method="post" id="formAddRole">
                                    <?php if (!empty($error)) : ?>
                                        <div class="alert alert-danger">
                                            <?php foreach ($error as $err) : ?>
                                                <p><?php echo $err; ?></p>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="fullname">Role Name (*)</label>
                                            <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter Role">
                                        </div>
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea name="description" class="form-control" id="description" placeholder="Enter Description"></textarea>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->

                                    <div class="card-footer">
                                        <button type="submit" name="add" class="btn btn-primary">Add</button>
                                    </div>
                                </form>

                                <?php if (isset($error['db_error'])): ?>
                                    <div class="alert alert-danger mt-2"><?php echo $error['db_error']; ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once '../../includes/footer.php'; ?>
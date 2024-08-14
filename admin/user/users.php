<?php
// Include the database configuration file
require_once '../../database/config.php';

// Start a new session or resume the existing session
session_start();

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['id'])) {
    header("Location: ../../authentication/login.php");
    exit;
}

// Retrieve the logged-in user's ID from the session
$id = $_SESSION['id'];

// Prepare and execute the SQL statement to fetch the logged-in user's data
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
$image = $data['image'];
$fname = $data['fname'];
$role = $data['role'];

// Prepare and execute the SQL statement to fetch all users along with their roles
$query = " SELECT * FROM tbluser";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$res = $stmt->get_result();

// Set page title and active menu
$title = "Users";
$active = "active";
?>

<?php include_once '../../includes/body.php'; ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo $title; ?></h1>
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
                            <div class="d-flex flex-row-reverse mb-3">
                                <a href="adduser.php" class="btn btn-primary">Add</a>
                            </div>
                            <table id="userstable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Number</th>
                                        <th>Gender</th>
                                        <th>Date of Birth</th>
                                        <th>Hobby</th>
                                        <th>Role</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($data = $res->fetch_assoc()) { ?>
                                        <tr>
                                            <td><?php echo $data['fname']; ?></td>
                                            <td><?php echo $data['lname']; ?></td>
                                            <td><?php echo $data['email']; ?></td>
                                            <td><?php echo $data['number']; ?></td>
                                            <td><?php echo $data['gender']; ?></td>
                                            <td><?php echo $data['dob']; ?></td>
                                            <td><?php echo $data['hobby']; ?></td>
                                            <td><?php echo $data['role']; ?></td>
                                            <td>
                                                <a href="adduser.php?id=<?php echo $data['id']; ?>" class="btn btn-success">Edit</a>
                                                <a href="adduser.php?idd=<?php echo $data['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once '../../includes/footer.php'; ?>
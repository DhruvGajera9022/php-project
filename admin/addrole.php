<?php
require_once '../database/config.php';
session_start();
session_regenerate_id(true);

if (!isset($_SESSION['id'])) {
    header("Location: ../authentication/login.php");
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

$image = htmlspecialchars($data['image']);
$fname = htmlspecialchars($data['fname']);

if (isset($_POST['add'])) {
    $fullname = $_POST['fullname'];
    $description = $_POST['description'];

    if (empty($fullname)) {
        $error['fullname'] = "Enter Full Name";
    }

    if (empty($description)) {
        $error['description'] = "Enter Description";
    }

    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO tblrole (name, description) VALUES (?, ?)");
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


$title = "Role";

?>


<?php include_once '../includes/body.php'; ?>

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
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="exampleInputEmail1">Full Name</label>
                                            <input type="text" class="form-control" id="exampleInputEmail1" name="fullname" placeholder="Enter Full Name">
                                        </div>
                                        <div class="form-group">
                                            <label for="exampleInputPassword1">Description</label>
                                            <input type="text" class="form-control" id="exampleInputPassword1" name="description" placeholder="Enter Description">
                                        </div>
                                    </div>
                                    <!-- /.card-body -->
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
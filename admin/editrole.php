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

if (isset($_POST['edit'])) {
    $fullname = $_POST['fullname'];
    $description = $_POST['description'];

    $error = [];
    if (empty($fullname)) {
        $error['fullname'] = "Enter Full Name";
    }

    if (empty($description)) {
        $error['description'] = "Enter Description";
    }

    if (empty($error)) {
        $upid = $_REQUEST['id'];
        $stmt = $conn->prepare("UPDATE tblrole SET name = ?, description = ? WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ssi", $fullname, $description, $upid);

        if ($stmt->execute()) {
            header("Location: roles.php");
            exit;
        } else {
            $error['db_error'] = "Database error: Failed to update";
        }
        $stmt->close();
    }
}

$upid = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

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
                                    <h3 class="card-title">Edit</h3>
                                </div>

                                <?php
                                if ($upid) {
                                    $stmt = $conn->prepare("SELECT * FROM tblrole WHERE id = ?");
                                    if (!$stmt) {
                                        die("Prepare failed: " . $conn->error);
                                    }
                                    $stmt->bind_param("i", $upid);
                                    $stmt->execute();
                                    $res = $stmt->get_result();

                                    if ($res->num_rows > 0) {
                                        $data = $res->fetch_assoc();
                                ?>

                                        <form method="post">
                                            <div class="card-body">
                                                <div class="form-group">
                                                    <label for="fullname">Full Name</label>
                                                    <input type="text" class="form-control" id="fullname" name="fullname" value="<?php echo htmlspecialchars($data['name']); ?>" placeholder="Enter Full Name">
                                                </div>
                                                <div class="form-group">
                                                    <label for="description">Description</label>
                                                    <input type="text" class="form-control" id="description" name="description" value="<?php echo htmlspecialchars($data['description']); ?>" placeholder="Enter Description">
                                                </div>
                                            </div>
                                            <!-- /.card-body -->
                                            <div class="card-footer">
                                                <button type="submit" name="edit" class="btn btn-primary">Edit</button>
                                            </div>
                                        </form>

                                <?php
                                    }
                                    $stmt->close();
                                }
                                ?>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once '../includes/footer.php'; ?>
<?php
require_once '../../database/config.php';
session_start();
session_regenerate_id(true);

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

$image = $data['image'];
$fname = $data['fname'];
$role = $data['role'];

// Initialize an array to store role names
$existingPolicy = [];

$queryRole = "SELECT name FROM tblpolicy";
$stmt = $conn->prepare($queryRole);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$resRoleName = $stmt->get_result();
while ($row = $resRoleName->fetch_assoc()) {
    $existingPolicy[] = $row['name'];
}
$stmt->close();

$upid = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

if ($upid) {
    $stmt = $conn->prepare("SELECT * FROM tblpolicy WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $upid);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $data = $res->fetch_assoc();
    }
    $stmt->close();
}

// Initialize an array to store error messages
$error = [];

if (!$upid) {
    $NAME = "Add";
} else {
    $NAME = "Edit";
}

if (isset($_POST['Add'])) {
    $fullname = trim($_POST['fullname']);
    $description = trim($_POST['description']);

    if (empty($fullname)) {
        $error['fullname'] = "Enter Full Name";
    }

    if (in_array($fullname, $existingPolicy)) {
        $error['role_name'] = "Role Name already exists";
    }

    // If there are no errors, proceed to insert the role into the database
    if (empty($error)) {
        $stmt = $conn->prepare("INSERT INTO tblpolicy (name, description) VALUES (?, ?)");
        if (!$stmt) {
            $error['db_error'] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("ss", $fullname, $description);
            if ($stmt->execute()) {
                header("Location: policy.php");
                exit;
            } else {
                $error['db_error'] = "Database error: Failed to register";
            }
            $stmt->close();
        }
    }
}

// Handle form submission
if (isset($_POST['Edit'])) {
    $fullname = $_POST['fullname'];
    $description = $_POST['description'];

    $error = [];
    if (empty($fullname)) {
        $error['fullname'] = "Enter Role";
    }

    if (in_array($fullname, $existingPolicy)) {
        $error['policy_name'] = "Role Name already exists";
    }

    if (empty($error)) {
        $stmt = $conn->prepare("UPDATE tblpolicy SET name = ?, description = ? WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("ssi", $fullname, $description, $upid);

        if ($stmt->execute()) {
            header("Location: policy.php");
            exit;
        } else {
            $error['db_error'] = "Database error: Failed to update";
        }
        $stmt->close();
    }
}

//Handel delete record
if (isset($_REQUEST['idd'])) {
    $id = $_GET['idd'];

    $query = "DELETE FROM tblpolicy WHERE id = '$id' ";
    $result = mysqli_query($conn, $query);

    if ($result) {
        header('location: policy.php');
    }
}


$title = "Policy";

?>

<?php include_once '../../includes/body.php'; ?>\

<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Policy</h1>
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
                                    <h3 class="card-title"><?php echo $NAME; ?></h3>
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
                                            <label for="fullname">Policy Name <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="fullname" name="fullname" placeholder="Enter Policy Name" value="<?php if (!$upid): echo "";
                                                                                                                                                            else: echo $data['name'];
                                                                                                                                                            endif; ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="policyDescription">Description</label>
                                            <textarea name="description" class="form-control" id="policyDescription" placeholder="Enter Description"><?php if (!$upid): echo "";
                                                                                                                                                        else: echo $data['description'];
                                                                                                                                                        endif; ?></textarea>
                                        </div>
                                    </div>
                                    <!-- /.card-body -->

                                    <div class="card-footer">
                                        <button type="submit" name="<?php echo $NAME; ?>" class="btn btn-primary">
                                            <?php echo $NAME; ?></button>
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
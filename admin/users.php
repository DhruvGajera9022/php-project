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

$title = "All Users";
$active = "active";
?>

<?php include_once '../includes/body.php'; ?>
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>All Users</h1>
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
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $query = "SELECT * FROM tbluser";
                                    $stmt = $conn->prepare($query);
                                    if (!$stmt) {
                                        die("Prepare failed: " . $conn->error);
                                    }
                                    if (!$stmt->execute()) {
                                        die("Execute failed: " . $stmt->error);
                                    }
                                    $res = $stmt->get_result();

                                    if ($res->num_rows > 0) {
                                        while ($data = $res->fetch_assoc()) {
                                    ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($data['fname']); ?></td>
                                                <td><?php echo htmlspecialchars($data['lname']); ?></td>
                                                <td><?php echo htmlspecialchars($data['email']); ?></td>
                                                <td><?php echo htmlspecialchars($data['number']); ?></td>
                                                <td><?php echo htmlspecialchars($data['gender']); ?></td>
                                                <td><?php echo htmlspecialchars($data['dob']); ?></td>
                                                <td><?php echo htmlspecialchars($data['hobby']); ?></td>
                                                <td>
                                                    <a href="../authentication/delete.php?id=<?php echo $data['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
                                                </td>
                                            </tr>
                                    <?php
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?php include_once '../includes/footer.php'; ?>
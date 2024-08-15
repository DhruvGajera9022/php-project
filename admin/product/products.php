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

$query = "SELECT * FROM tblmaster";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$res = $stmt->get_result();

$title = "Products";

?>

<?php include_once '../../includes/body.php'; ?>


<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Master</h1>
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
                            <div class="d-flex flex-row-reverse"><a href="addproduct.php" class="btn btn-primary">Add</a></div>
                            <table id="tableProduct" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Slug</th>
                                        <th>Category</th>
                                        <th>Size</th>
                                        <th>Color</th>
                                        <th>Weight</th>
                                        <th>Old Price</th>
                                        <th>New Price</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($data = $res->fetch_assoc()) { ?>
                                        <tr>
                                            <td><img src="../../assets/img/productimage/<?php echo $data['images']; ?>" height="50px" alt="product image"></td>
                                            <td><?php echo $data['name']; ?></td>
                                            <td><?php echo $data['slug']; ?></td>
                                            <td><?php echo $data['category']; ?></td>
                                            <td><?php echo $data['size']; ?></td>
                                            <td><input type="color" name="" id="" disabled value="<?php echo $data['color']; ?>"></td>
                                            <td><?php echo $data['weight']; ?></td>
                                            <td><?php echo $data['oldprice']; ?></td>
                                            <td><?php echo $data['newprice']; ?></td>
                                            <td><?php echo $data['status']; ?></td>
                                            <td>
                                                <a href="addproduct.php?id=<?php echo $data['id']; ?>" class="btn btn-success">Edit</a>
                                                <a href="addproduct.php?idd=<?php echo $data['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
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
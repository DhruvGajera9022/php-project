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



$title = "Products";

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
                                        <label for="psize">Select Size (*)</label>
                                        <select name="psize" id="psize" class="form-control">
                                            <option value="">Select Size</option>
                                            <option value="">XS</option>
                                            <option value="">S</option>
                                            <option value="">M</option>
                                            <option value="">L</option>
                                            <option value="">XL</option>
                                            <option value="">XXL</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label for="pcolor">Select Color (*)</label>
                                        <input type="color" name="pcolor" id="pcolor" class="form-control">
                                    </div>
                                    <div class="form-group">
                                        <label for="pweight">Enter Weigth (*)</label>
                                        <input type="number" name="pweight" id="pweight" class="form-control">
                                    </div>
                                </div>
                                <!-- /.card-body -->

                                <div class="card-footer">
                                    <button type="submit" name="Add" class="btn btn-primary">
                                        Add</button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>







<?php include_once '../../includes/footer.php'; ?>
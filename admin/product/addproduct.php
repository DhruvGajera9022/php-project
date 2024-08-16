<?php

// Include Database
require_once '../../database/config.php';

// Start the session
session_start();

// If user not logged in the redirected to login page
if (!isset($_SESSION['id'])) {
    header("Location: ../../authentication/login.php");
    exit;
}

// Get the id of user from session and fetch data of user
$id = $_SESSION['id'];
if ($id) {
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
}

// Initialize an array to store slugs
$existingSlug = [];

// Fetch the slug from tblmaster for compare the slug
$querySlug = "SELECT slug FROM tblmaster";
$stmt = $conn->prepare($querySlug);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}
$resSlugName = $stmt->get_result();
while ($row = $resSlugName->fetch_assoc()) {
    $existingSlug[] = $row['slug'];
}
$stmt->close();

// Get the update id from update button products.php
$upid = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

// Fetch the data of update id
if ($upid) {
    $stmt = $conn->prepare("SELECT * FROM tblmaster WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $upid);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $data = $res->fetch_assoc();
    }

    $fetchStatus1 = $data['status'];
    $fetchStatus = explode(', ', $fetchStatus1);

    $stmt->close();
}

// Initialize an array to store errors
$errors = [];

// For title and buttons
if (!$upid) {
    $NAME = "Add";
} else {
    $NAME = "Edit";
}

// To insert the record
if (isset($_POST['Add'])) {
    $pname = $_POST['pname'];
    $pdescription = $_POST['pdescription'];
    $pslug = $_POST['pslug'];
    $pcategory = $_POST['pcategory'];
    $psize = $_POST['psize'];
    $pcolor = $_POST['pcolor'];
    $pweight = $_POST['pweight'];
    $poldprice = $_POST['poldprice'];
    $pnewprice = $_POST['pnewprice'];
    $pstatus = $_POST['pstatus'];

    // image array for multiple image
    $images = [];
    $image_count = count($_FILES['image']['name']);

    if (empty($pstatus)) {
        $pstatus[] = "Out of Stock";
        $errors['pstatus'] = "";
    }
    $strStatus = implode(', ', $pstatus);

    // Validate inputs
    if (empty($pname)) {
        $errors['pname'] = "Enter product name";
    }

    if (empty($pdescription)) {
        $errors['pdescription'] = "Enter product description";
    }

    if (empty($pslug)) {
        $errors['pslug'] = "Enter slug";
    }

    if (in_array($pslug, $existingSlug)) {
        $errors['slug'] = "Slug already exists";
    }

    if (empty($pcategory)) {
        $errors['pcategory'] = "Select category";
    }

    if (empty($psize)) {
        $errors['psize'] = "Select size";
    }

    if (empty($pcolor)) {
        $errors['pcolor'] = "Please select color";
    }

    if (empty($pweight)) {
        $errors['pweight'] = "Enter wight";
    }

    if (empty($poldprice)) {
        $errors['poldprice'] = "Enter old price";
    }

    if (empty($pnewprice)) {
        $errors['pnewprice'] = "Enter new price";
    }

    // for loop is used for take multiple image and insert into database
    for ($i = 0; $i < $image_count; $i++) {
        $new_image = $_FILES['image']['name'][$i];
        $temp_name = $_FILES['image']['tmp_name'][$i];
        $folder = "../../assets/img/productimage/" . basename($new_image);

        // Validate and move each file
        if ($new_image != '') {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['image']['type'][$i], $allowed_types)) {
                if ($_FILES['image']['size'][$i] < 5000000) { // 5MB limit
                    if (move_uploaded_file($temp_name, $folder)) {
                        $images[] = basename($new_image); // Save the file name
                    } else {
                        $errors['image'] = "Error uploading file.";
                    }
                } else {
                    $errors['image'] = "Image size exceeds 5MB.";
                }
            } else {
                $errors['image'] = "Invalid image format. Only JPEG, PNG, and GIF are allowed.";
            }
        }
    }

    // Store image array data in separate words and delemeter is , 
    $images_string = implode(', ', $images); // Convert array to a string

    // Check if array of errors is empty the insert query is executed
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO tblmaster (name, description, slug, category, size, color, weight, oldprice, newprice, images, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            $errors['db_error'] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("sssssssssss", $pname, $pdescription, $pslug, $pcategory, $psize, $pcolor, $pweight, $poldprice, $pnewprice, $images_string, $strStatus);
            if ($stmt->execute()) {
                header("Location: products.php");
                exit;
            } else {
                $errors['db_error'] = "Database error: Failed to register";
            }
            $stmt->close();
        }
    }
}

// To edit the record
if (isset($_POST['Edit'])) {
    $pname = $_POST['pname'];
    $pdescription = $_POST['pdescription'];
    $pslug = $_POST['pslug'];
    $pcategory = $_POST['pcategory'];
    $psize = $_POST['psize'];
    $pcolor = $_POST['pcolor'];
    $pweight = $_POST['pweight'];
    $poldprice = $_POST['poldprice'];
    $pnewprice = $_POST['pnewprice'];
    $pstatus = $_POST['pstatus'];
    $old_images = explode(', ', $_POST['image_old']); // Split the old images into an array

    // image array for multiple image
    $images = [];
    $image_count = count($_FILES['image']['name']);

    if (empty($pstatus)) {
        $pstatus[] = "Out of Stock";
    }
    $strStatus = implode(", ", $pstatus);

    // Validate inputs
    if (empty($pname)) {
        $errors['pname'] = "Enter product name";
    }

    if (empty($pdescription)) {
        $errors['pdescription'] = "Enter product description";
    }

    if (empty($pslug)) {
        $errors['pslug'] = "Enter slug";
    }

    if (in_array($pslug, $existingSlug)) {
        $errors['slug'] = "Slug already exists";
    }

    if (empty($pcategory)) {
        $errors['pcategory'] = "Select category";
    }

    if (empty($psize)) {
        $errors['psize'] = "Select size";
    }

    if (empty($pcolor)) {
        $errors['pcolor'] = "Please select color";
    }

    if (empty($pweight)) {
        $errors['pweight'] = "Enter wight";
    }

    if (empty($poldprice)) {
        $errors['poldprice'] = "Enter old price";
    }

    if (empty($pnewprice)) {
        $errors['pnewprice'] = "Enter new price";
    }

    // for loop is used for take multiple image and insert into database
    for ($i = 0; $i < $image_count; $i++) {
        $new_image = $_FILES['image']['name'][$i];
        $temp_name = $_FILES['image']['tmp_name'][$i];
        $folder = "../../assets/img/productimage/" . basename($new_image);

        // check the images and types of it
        if ($new_image != '') {
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($_FILES['image']['type'][$i], $allowed_types)) {
                if ($_FILES['image']['size'][$i] < 5000000) { // 5MB limit
                    if (move_uploaded_file($temp_name, $folder)) {
                        $images[] = basename($new_image); // Save the file name
                    } else {
                        $errors['image'] = "Error uploading file.";
                    }
                } else {
                    $errors['image'] = "Image size exceeds 5MB.";
                }
            } else {
                $errors['image'] = "Invalid image format. Only JPEG, PNG, and GIF are allowed.";
            }
        }
    }

    // check the images and if images already available then store new images and remove old images from the folder
    if (!empty($images)) {
        foreach ($old_images as $old_image) {
            if (!empty($old_image) && file_exists("../../assets/img/productimage/" . $old_image)) {
                unlink("../../assets/img/productimage/" . $old_image);
            }
        }
        $images_string = implode(', ', $images); // Convert new images array to a string
    } else {
        $images_string = implode(', ', $old_images); // If no new images are uploaded, keep the old images
    }

    // check the array of errors is empty then update query is executed
    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE tblmaster SET name = ?, description = ?, slug = ?, category = ?, size = ?, color = ?, weight = ?, oldprice = ?, newprice = ?, images = ?, status = ? WHERE id = ?");
        if (!$stmt) {
            $errors['db_error'] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("sssssssssssi", $pname, $pdescription, $pslug, $pcategory, $psize, $pcolor, $pweight, $poldprice, $pnewprice, $images_string, $strStatus, $upid);
            if ($stmt->execute()) {
                header("Location: products.php");
                exit;
            } else {
                $errors['db_error'] = "Database error: Failed to register";
            }
            $stmt->close();
        }
    }
}

// To delete the record
if (isset($_REQUEST['idd'])) {
    $id = intval($_GET['idd']); // Use intval() to ensure it's an integer

    // Fetch the product details to get the image filenames
    $stmt = $conn->prepare("SELECT images FROM tblmaster WHERE id = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $dataDelete = $res->fetch_assoc();
        $images = explode(', ', $dataDelete['images']); // Convert the comma-separated string to an array

        // Delete the product record from the database
        $stmt = $conn->prepare("DELETE FROM tblmaster WHERE id = ?");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // Delete images from the server
            foreach ($images as $image) {
                $imagePath = "../../assets/img/productimage/" . $image;
                if (file_exists($imagePath)) {
                    unlink($imagePath); // Delete the file
                }
            }
            header('Location: products.php'); // Redirect to the products page
            exit;
        } else {
            die("Database error: Failed to delete record");
        }
    } else {
        die("Product not found");
    }
}


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
                            <div class="card card-primary">
                                <div class="card-header">
                                    <h3 class="card-title"><?php echo $NAME; ?></h3>
                                </div>
                                <form method="post" id="pform" enctype="multipart/form-data">

                                    <!-- To display errors -->
                                    <?php if (!empty($errors)) : ?>
                                        <div class="alert alert-danger">
                                            <?php foreach ($errors as $err) : ?>
                                                <p><?php echo $err; ?></p>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="card-body">

                                        <!-- Product Name -->
                                        <div class="form-group row">
                                            <label for="pname" class="col-sm-2 col-form-label">Product Name <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="pname" id="pname" class="form-control" value="<?php echo $upid ? $data['name'] : ''; ?>">
                                            </div>
                                        </div>

                                        <!-- Product Description -->
                                        <div class="form-group row">
                                            <label for="pdescription" class="col-sm-2 col-form-label">Product Description <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <textarea name="pdescription" id="pdescription" class="form-control">
            <?php echo $upid ? $data['description'] : ''; ?>
        </textarea>
                                            </div>
                                        </div>

                                        <!-- Product Slug -->
                                        <div class="form-group row">
                                            <label for="pslug" class="col-sm-2 col-form-label">Product Slug <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="text" name="pslug" id="pslug" class="form-control" value="<?php echo $upid ? $data['slug'] : ''; ?>">
                                            </div>
                                        </div>

                                        <!-- Product Category -->
                                        <div class="form-group row">
                                            <label for="pcategory" class="col-sm-2 col-form-label">Category <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <select name="pcategory" id="pcategory" class="form-control">
                                                    <option value="">Select Category</option>
                                                    <option value="Cloths" <?php if (!$upid) {
                                                                            } else {
                                                                                if ($data['category'] == "Cloths") echo 'selected="selected"';
                                                                            } ?>>Cloths</option>
                                                    <option value="Shoes" <?php if (!$upid) {
                                                                            } else {
                                                                                if ($data['category'] == "Shoes") echo 'selected="selected"';
                                                                            } ?>>Shoes</option>
                                                    <option value="Toys" <?php if (!$upid) {
                                                                            } else {
                                                                                if ($data['category'] == "Toys") echo 'selected="selected"';
                                                                            } ?>>Toys</option>
                                                    <option value="Mobiles" <?php if (!$upid) {
                                                                            } else {
                                                                                if ($data['category'] == "Mobiles") echo 'selected="selected"';
                                                                            } ?>>Mobiles</option>
                                                    <option value="Laptops" <?php if (!$upid) {
                                                                            } else {
                                                                                if ($data['category'] == "Laptops") echo 'selected="selected"';
                                                                            } ?>>Laptops</option>
                                                    <option value="Grocery<?php if (!$upid) {
                                                                            } else {
                                                                                if ($data['category'] == "Grocery") echo 'selected="selected"';
                                                                            } ?>">Grocery</option>
                                                    <option value="Stationery" <?php if (!$upid) {
                                                                                } else {
                                                                                    if ($data['category'] == "Stationery") echo 'selected="selected"';
                                                                                } ?>>Stationery</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Product Size -->
                                        <div class="form-group row">
                                            <label for="psize" class="col-sm-2 col-form-label">Size <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <select name="psize" id="psize" class="form-control">
                                                    <option value="">Select Size</option>
                                                    <option value="XS" <?php if (!$upid) {
                                                                        } else {
                                                                            if ($data['size'] == "XS") echo 'selected="selected"';
                                                                        } ?>>XS</option>
                                                    <option value="S" <?php if (!$upid) {
                                                                        } else {
                                                                            if ($data['size'] == "S") echo 'selected="selected"';
                                                                        } ?>>S</option>
                                                    <option value="M" <?php if (!$upid) {
                                                                        } else {
                                                                            if ($data['size'] == "M") echo 'selected="selected"';
                                                                        } ?>>M</option>
                                                    <option value="L" <?php if (!$upid) {
                                                                        } else {
                                                                            if ($data['size'] == "L") echo 'selected="selected"';
                                                                        } ?>>L</option>
                                                    <option value="XL" <?php if (!$upid) {
                                                                        } else {
                                                                            if ($data['size'] == "XL") echo 'selected="selected"';
                                                                        } ?>>XL</option>
                                                    <option value="XXL" <?php if (!$upid) {
                                                                        } else {
                                                                            if ($data['size'] == "XXL") echo 'selected="selected"';
                                                                        } ?>>XXL</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Product Color -->
                                        <div class="form-group row">
                                            <label for="pcolor" class="col-sm-2 col-form-label">Color <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="color" name="pcolor" id="pcolor" class="form-control" value="<?php echo $upid ? $data['color'] : ''; ?>">
                                            </div>
                                        </div>

                                        <!-- Product Weight -->
                                        <div class="form-group row">
                                            <label for="pweight" class="col-sm-2 col-form-label">Weigth <span class="text-danger">*</span></label>
                                            <div class="col-sm-10">
                                                <input type="number" name="pweight" id="pweight" class="form-control" value="<?php echo $upid ? $data['weight'] : ''; ?>">
                                            </div>
                                        </div>

                                        <!-- Product Old Price - New Price -->
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <!-- Product Old Price -->
                                                <div class="form-group row">
                                                    <label for="poldprice" class="col-sm-2 col-form-label col-sm-3">Old Price <span class="text-danger">*</span></label>
                                                    <div class="col-sm-9">
                                                        <input type="number" name="poldprice" id="poldprice" class="form-control" value="<?php echo $upid ? $data['oldprice'] : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <!-- Product New Price -->
                                                <div class="form-group row">
                                                    <label for="pnewprice" class="col-sm-2 col-form-label col-sm-3">New Price <span class="text-danger">*</span></label>
                                                    <div class="col-sm-9">
                                                        <input type="number" name="pnewprice" id="pnewprice" class="form-control" value="<?php echo $upid ? $data['newprice'] : ''; ?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Product Image - Status -->
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <!-- Product Image -->
                                                <div class="form-group row">
                                                    <label for="inputImage" class="col-sm-2 col-form-label col-sm-3">Product Image <span class="text-danger">*</span></label>
                                                    <div class="col-sm-9">
                                                        <input type="file" class="form-control" name="image[]" id="inputImage" multiple>
                                                        <input type="hidden" name="image_old" value="<?php if (!$upid): echo "";
                                                                                                        else: echo $data['images'];
                                                                                                        endif ?>">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-sm-6">
                                                <!-- Product Status -->
                                                <div class="form-group row">
                                                    <label class="col-sm-2 col-form-label col-sm-3">Status <span class="text-danger">*</span></label>
                                                    <div class="col-sm-9">
                                                        <label for="pstatus" class="col-sm-2 col-form-label"><input type="checkbox" name="pstatus[]" id="pstatus" value="Active" <?php echo $upid ? in_array("Active", $fetchStatus) ? "checked" : "" : '' ?>> Active</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.card-body -->

                                    <div class="card-footer">
                                        <button type="submit" name="<?php echo $NAME; ?>" class="btn btn-primary">
                                            <?php echo $NAME; ?></button>
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
<?php
session_start();
include('../db_conn.php');
global $conn;
// 1. Security Check
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

// 2. Product ka data fetch karna ID ke zariye
if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $get_product = mysqli_query($conn, "SELECT * FROM products WHERE id='$id'");
    
    if(mysqli_num_rows($get_product) > 0) {
        $pdata = mysqli_fetch_assoc($get_product);
    } else {
        header("Location: view_products.php");
        exit();
    }
} else {
    header("Location: view_products.php");
    exit();
}

// 3. Update Logic
if (isset($_POST['update_product_btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['pro_name']);
    $price = mysqli_real_escape_string($conn, $_POST['pro_price']);
    $old_price = mysqli_real_escape_string($conn, $_POST['pro_old_price']); // Naya data receive kiya
    $category = mysqli_real_escape_string($conn, $_POST['pro_category']);
    $desc = mysqli_real_escape_string($conn, $_POST['pro_desc']);
    
    // Image Handling (Agar nayi image select ki toh wo lo, warna purani rehne do)
    $image1 = !empty($_FILES['pro_image']['name']) ? $_FILES['pro_image']['name'] : $pdata['pro_image'];
    $image2 = !empty($_FILES['pro_image2']['name']) ? $_FILES['pro_image2']['name'] : $pdata['pro_image2'];
    $image3 = !empty($_FILES['pro_image3']['name']) ? $_FILES['pro_image3']['name'] : $pdata['pro_image3'];

    $folder = "../image/";

    // Updated Query: pro_old_price ko bhi shamil kar diya
    $update_query = "UPDATE products SET 
                     pro_name='$name', 
                     pro_price='$price', 
                     pro_old_price='$old_price', 
                     pro_category='$category', 
                     pro_image='$image1', 
                     pro_image2='$image2', 
                     pro_image3='$image3', 
                     pro_desc='$desc' 
                     WHERE id='$id'";

    if(mysqli_query($conn, $update_query)) {
        // Sirf unhi images ko move karein jo upload ki gayi hain
        if(!empty($_FILES['pro_image']['name'])) move_uploaded_file($_FILES['pro_image']['tmp_name'], $folder.$image1);
        if(!empty($_FILES['pro_image2']['name'])) move_uploaded_file($_FILES['pro_image2']['tmp_name'], $folder.$image2);
        if(!empty($_FILES['pro_image3']['name'])) move_uploaded_file($_FILES['pro_image3']['tmp_name'], $folder.$image3);
        
        $success = "Product updated successfully!";
        // Data refresh karein
        header("Location: view_products.php?msg=Updated");
        exit();
    } else {
        $error = "Update fail: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product - Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container">
    <div class="edit-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold m-0">Edit Product</h2>
            <a href="view_products.php" class="btn btn-light rounded-pill px-4 border">Back</a>
        </div>

        <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="section-label">Basic Information</div>
            <div class="row mb-4">
                <div class="col-md-12 mb-3">
                    <label class="form-label small fw-bold">Product Name</label>
                    <input type="text" name="pro_name" class="form-control" value="<?php echo htmlspecialchars($pdata['pro_name']); ?>" required>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-bold text-success">Sale Price (Real Qimat)</label>
                    <input type="number" name="pro_price" class="form-control border-success" value="<?php echo htmlspecialchars($pdata['pro_price']); ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-bold text-danger">Compare Price (Purani Qimat)</label>
                    <input type="number" name="pro_old_price" class="form-control" value="<?php echo htmlspecialchars($pdata['pro_old_price'] ?? ''); ?>" placeholder="Cut price (e.g. 2800)">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label small fw-bold">Category</label>
                    <select name="pro_category" class="form-select">
                        <option value="Electronic" <?php if($pdata['pro_category']=='Electronic') echo 'selected'; ?>>Electronic</option>
                        <option value="Kitchen" <?php if($pdata['pro_category']=='Kitchen') echo 'selected'; ?>>Kitchen</option>
                        <option value="Cream" <?php if($pdata['pro_category']=='Cream') echo 'selected'; ?>>Cream</option>
                    </select>
                </div>
            </div>

            <div class="section-label">Media & Images</div>
            <div class="row mb-4 text-center">
                <div class="col-md-4">
                    <label class="small fw-bold d-block">Main Image</label>
                    <img src="../image/<?php echo $pdata['pro_image']; ?>" class="current-img mb-2">
                    <input type="file" name="pro_image" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold d-block">Image 2</label>
                    <img src="../image/<?php echo $pdata['pro_image2']; ?>" class="current-img mb-2">
                    <input type="file" name="pro_image2" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                    <label class="small fw-bold d-block">Image 3</label>
                    <img src="../image/<?php echo $pdata['pro_image3']; ?>" class="current-img mb-2">
                    <input type="file" name="pro_image3" class="form-control form-control-sm">
                </div>
            </div>

            <div class="section-label">Product Description</div>
            <div class="mb-4">
                <textarea name="pro_desc" class="form-control" rows="5"><?php echo htmlspecialchars($pdata['pro_desc']); ?></textarea>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" name="update_product_btn" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm" style="background-color: #4318FF; border: none;">Save Changes</button>
                <a href="view_products.php" class="btn btn-link text-muted">Cancel</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
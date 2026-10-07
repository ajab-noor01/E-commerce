<?php
session_start();
include('../db_conn.php');
global $conn;
// 1. Security Check
if(!isset($_SESSION['admin_logged_in'])) { 
    header("Location: login.php"); 
    exit(); 
}

// 2. Secure Deactivate Logic (Delete ki jagah Soft Delete / Hide lagaya hai)
if(isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    
    // Delete karne ke bajaye status ko 0 (Deactivated) kar rahe hain
    $stmt = $conn->prepare("UPDATE products SET pro_status = 0 WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        $stmt->close();
        header("Location: view_products.php?msg=Product Deactivated Successfully!");
        exit();
    }
}

// 3. Secure Filter & Search Logic (Sirf active products ko show karne ke liye)
$search_query = "";
$res = null;

if(isset($_GET['admin_search']) && !empty(trim($_GET['admin_search']))) {
    $search = "%" . trim($_GET['admin_search']) . "%";
    $exact_cat = trim($_GET['admin_search']);
    
    // Query mein pro_status = 1 lagaya taaki deactivated items search mein na aayin
    $stmt = $conn->prepare("SELECT * FROM products WHERE (pro_name LIKE ? OR pro_category = ?) AND pro_status = 1 ORDER BY id DESC");
    $stmt->bind_param("ss", $search, $exact_cat);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();
} else {
    // Bina search ke sirf active products list honge
    $res = mysqli_query($conn, "SELECT * FROM products WHERE pro_status = 1 ORDER BY id DESC");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management |ZeldaStores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0">Product Inventory</h2>
            <p class="text-muted small m-0">Monitor and manage store stock levels</p>
        </div>
        <div>
            <a href="dashboard.php" class="btn btn-light border rounded-pill px-4 me-2">Dashboard</a>
            <a href="add_product.php" class="btn btn-primary rounded-pill px-4" style="background: #4318FF; border: none;">+ Add New</a>
        </div>
    </div>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4 fw-semibold small"><?php echo htmlspecialchars($_GET['msg']); ?></div>
    <?php endif; ?>

    <div class="card main-card p-4">
        <form method="GET" class="mb-4" autocomplete="off">
            <div class="input-group" style="max-width: 400px;">
                <input type="text" name="admin_search" class="form-control border-0 bg-light p-3" placeholder="Search by name or category..." value="<?php echo isset($_GET['admin_search']) ? htmlspecialchars($_GET['admin_search']) : ''; ?>" style="border-radius: 15px 0 0 15px;">
                <button class="btn btn-primary px-4" style="background: #4318FF; border: none; border-radius: 0 15px 15px 0;"><i class="bi bi-search"></i></button>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Product Details</th>
                        <th>Category</th>
                        <th>Pricing System</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($res && $res->num_rows > 0): ?>
                        <?php while($row = $res->fetch_assoc()): ?>
                        <tr>
                            <td><img src="../image/<?php echo htmlspecialchars($row['pro_image']); ?>" class="product-img shadow-sm"></td>
                            <td>
                                <div class="fw-bold text-navy"><?php echo htmlspecialchars($row['pro_name']); ?></div>
                                <small class="text-muted">ID: #<?php echo $row['id']; ?></small>
                            </td>
                            <td><span class="badge-cat"><?php echo htmlspecialchars($row['pro_category']); ?></span></td>
                            <td>
                                <div class="price-new">Rs. <?php echo number_format($row['pro_price']); ?></div>
                                <?php if(!empty($row['pro_old_price'])): ?>
                                    <div class="price-old">Rs. <?php echo number_format($row['pro_old_price']); ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border-0 text-warning fs-5 me-2" title="Edit Item"><i class="bi bi-pencil-square"></i></a>
                                <a href="view_products.php?delete_id=<?php echo $row['id']; ?>" class="btn btn-sm btn-light border-0 text-danger fs-5" onclick="return confirm('Are you sure you want to delete this product? This action cannot be undone.')" title="Delete Item"><i class="bi bi-trash3"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center py-5 text-muted fw-semibold">No inventory products found matching the criteria.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
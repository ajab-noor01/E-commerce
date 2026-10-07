<?php 
session_start();
require_once('../db_conn.php'); 

// Security Check
if(!isset($_SESSION['admin_logged_in'])) { 
    header("Location: login.php"); 
    exit(); 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
</head>
<body class="bg-light py-5">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white p-4 rounded-top-4">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-plus-circle me-2"></i>Add New Product</h4>
                </div>
                <div class="card-body p-4">
                    <form action="insert_product.php" method="POST" enctype="multipart/form-data">
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Product Name</label>
                            <input type="text" name="pro_name" class="form-control form-control-lg" required>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Selling Price (PKR)</label>
                                <input type="number" step="0.01" name="pro_price" class="form-control" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Purchase Price / Cost (PKR)</label>
                                <input type="number" step="0.01" name="purchase_price" class="form-control" placeholder="0.00">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Category</label>
                                <select name="pro_category" class="form-select">
                                    <option value="Electronic">Electronic</option>
                                    <option value="Kitchen">Kitchen</option>
                                    <option value="Beauty">Beauty</option>
                                    <option value="Auto">Auto</option>
                                    <option value="General" selected>General</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Stock Quantity</label>
                                <input type="number" name="product_stock" class="form-control" value="50" required>
                            </div>
                        </div>

                        <!-- 3 IMAGES UPLOAD SECTION -->
                        <div class="border rounded-3 p-3 bg-white mb-3">
                            <label class="form-label fw-bold text-primary mb-2">Product Gallery Images (Up to 3)</label>
                            
                            <div class="mb-2">
                                <label class="form-label small text-muted">Main Image 1 (Required)</label>
                                <input type="file" name="pro_image" class="form-control" accept="image/*" required>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small text-muted">Image 2 (Optional)</label>
                                <input type="file" name="pro_image2" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-1">
                                <label class="form-label small text-muted">Image 3 (Optional)</label>
                                <input type="file" name="pro_image3" class="form-control" accept="image/*">
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="pro_desc" class="form-control" rows="3"></textarea>
                        </div>

                        <button type="submit" name="submit" class="btn btn-primary btn-lg w-100 rounded-pill fw-bold">Save Product</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
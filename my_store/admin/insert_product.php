<?php
session_start();
require_once('../db_conn.php');

if(!isset($_SESSION['admin_logged_in'])) { 
    header("Location: login.php"); 
    exit(); 
}

if(isset($_POST['submit'])) {
    $pro_name = mysqli_real_escape_string($conn, $_POST['pro_name']);
    $pro_price = floatval($_POST['pro_price']);
    $purchase_price = isset($_POST['purchase_price']) ? floatval($_POST['purchase_price']) : 0.00;
    $pro_category = mysqli_real_escape_string($conn, $_POST['pro_category']);
    $product_stock = intval($_POST['product_stock']);
    $pro_desc = mysqli_real_escape_string($conn, $_POST['pro_desc']);

    // Target Directory
    $target_dir = "../image/";

    // Helper function for uploading images
    function uploadImage($fileKey, $target_dir) {
        if(isset($_FILES[$fileKey]) && $_FILES[$fileKey]['error'] == 0) {
            $fileName = time() . '_' . rand(100,999) . '_' . basename($_FILES[$fileKey]["name"]);
            $targetFilePath = $target_dir . $fileName;
            if(move_uploaded_file($_FILES[$fileKey]["tmp_name"], $targetFilePath)) {
                return $fileName;
            }
        }
        return null;
    }

    $img1 = uploadImage('pro_image', $target_dir);
    $img2 = uploadImage('pro_image2', $target_dir);
    $img3 = uploadImage('pro_image3', $target_dir);

    // Prepared Statement for SQL Injection prevention
    $stmt = $conn->prepare("INSERT INTO products (pro_name, pro_price, purchase_price, pro_category, product_stock, pro_image, pro_image2, pro_image3, pro_desc) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sddsissss", $pro_name, $pro_price, $purchase_price, $pro_category, $product_stock, $img1, $img2, $img3, $pro_desc);

    if($stmt->execute()) {
        header("Location: view_products.php?msg=added");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
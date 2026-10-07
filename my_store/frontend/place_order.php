<?php 
include('../db_conn.php'); 
global $conn;

if (isset($_POST['order_btn'])) {
    // 1. Form se data lena aur trim (khali spaces saaf) karna
    $p_id    = trim($_POST['product_id']);
    $name    = trim($_POST['c_name']);
    $phone   = trim($_POST['c_phone']);
    $address = trim($_POST['c_address']);

    // 2. VALIDATION LAYER: Check karein koi field khali toh nahi hai
    if (empty($p_id) || empty($name) || empty($phone) || empty($address)) {
        echo "<script>
                alert('Error: Please fill all fields to place your order!');
                window.history.back();
              </script>";
        exit();
    }

    // 3. SECURE SQL: Prepared Statement ka istemal (Hack-Proof)
    $stmt = $conn->prepare("INSERT INTO orders (product_id, c_name, c_phone, c_address, status) VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("isss", $p_id, $name, $phone, $address); // 'i' for integer id, 's' for strings

    if ($stmt->execute()) {
        $stmt->close();
        // Professional Success Message for UrbanDeals
        echo "<script>
                alert('Thank you! Your order has been placed successfully. Our team will contact you soon.');
                window.location.href='index.php';
              </script>";
        exit();
    } else {
        // Live server par error chhupane ke liye safe message
        echo "Order Processing Error: Galti ki wajah se order confirm nahi ho saka.";
    }
} else {
    // Agar koi direct is file ka URL khole toh use homepage par bhej do
    header("Location: index.php");
    exit();
}
?>
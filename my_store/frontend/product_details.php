<?php 
include('../db_conn.php');
global $conn;
// 1. SECURE CHECK: ID receive karna aur Prepared Statement chalana
if(isset($_GET['id'])) {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? LIMIT 1");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if($res && $res->num_rows > 0) {
        $product = $res->fetch_assoc();
    } else {
        header("Location: index.php");
        exit();
    }
    $stmt->close();
} else {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['pro_name']); ?> |ZeldaStores Premium</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
     <link rel="stylesheet" href="../style.css"> 
</head>
<body>

<div class="container py-5">
    <a href="index.php" class="btn btn-light border rounded-pill px-4 mb-4 shadow-sm fw-bold">
        <i class="bi bi-arrow-left me-2"></i>Return to Shop
    </a>

    <div class="card details-card shadow-sm">
        <div class="row g-0">
            <div class="col-md-6 border-end">
                <div class="main-img-container">
                    <img src="../image/<?php echo htmlspecialchars($product['pro_image']); ?>" id="mainView" class="main-img" alt="product">
                </div>
                <div class="d-flex justify-content-center gap-3 pb-4">
                    <img src="../image/<?php echo htmlspecialchars($product['pro_image']); ?>" class="thumb-img shadow-sm" onclick="changeImg(this.src)">
                    <?php if(!empty($product['pro_image2'])): ?>
                        <img src="../image/<?php echo htmlspecialchars($product['pro_image2']); ?>" class="thumb-img shadow-sm" onclick="changeImg(this.src)">
                    <?php endif; ?>
                    <?php if(!empty($product['pro_image3'])): ?>
                        <img src="../image/<?php echo htmlspecialchars($product['pro_image3']); ?>" class="thumb-img shadow-sm" onclick="changeImg(this.src)">
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="p-4 p-lg-5">
                    <span class="badge bg-primary mb-2 px-3 py-2 text-uppercase fw-bold" style="background-color: #4318FF !important;"><?php echo htmlspecialchars($product['pro_category']); ?></span>
                    <h1 class="fw-bold mb-3" style="color: #1b2559;"><?php echo htmlspecialchars($product['pro_name']); ?></h1>
                    
                    <div class="price-container">
                        <div class="price-tag">Rs. <?php echo number_format($product['pro_price']); ?></div>
                        <?php if(!empty($product['pro_old_price'])): ?>
                            <div class="old-price-tag">Rs. <?php echo number_format($product['pro_old_price']); ?></div>
                        <?php endif; ?>
                    </div>
                    
                    <p class="text-muted lh-base mb-4" style="font-size: 0.95rem;">
                        <?php echo nl2br(htmlspecialchars($product['pro_desc'])); ?>
                    </p>

                    <div class="quick-order-box mb-4">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-truck text-primary me-2"></i> Cash on Delivery Order Form</h6>
                        <form action="place_order.php" method="POST" autocomplete="off">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <div class="row g-2">
                                <div class="col-12 mb-2">
                                    <input type="text" name="c_name" class="form-control" placeholder="Your Full Name" required>
                                </div>
                                <div class="col-12 mb-2">
                                    <input type="text" name="c_phone" class="form-control" placeholder="WhatsApp or Phone Number" required>
                                </div>
                                <div class="col-12 mb-3">
                                    <textarea name="c_address" class="form-control" rows="2" placeholder="Complete Home/Shop Address with City Name" required></textarea>
                                </div>
                                <div class="col-12">
                                    <button type="submit" name="order_btn" class="btn btn-primary w-100 fw-bold py-3 shadow-sm text-uppercase">
                                        <i class="bi bi-cart-check-fill me-2"></i> Place Order Now (COD)
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <?php 
                        $my_number = "923044304440"; 
                        $msg = "Assalam-o-Alaikum, mujhe ye product chahiye:\n\n*Product:* " . $product['pro_name'] . "\n*Price:* Rs. " . number_format($product['pro_price']);
                        $wa_link = "https://wa.me/" . $my_number . "?text=" . urlencode($msg);
                    ?>
                    <a href="<?php echo $wa_link; ?>" target="_blank" class="btn btn-whatsapp w-100 py-3 fw-bold text-uppercase text-decoration-none d-block text-center shadow-sm">
                        <i class="bi bi-whatsapp me-2"></i> Order via WhatsApp Chat
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<a href="https://wa.me/<?php echo $my_number; ?>" class="whatsapp-float" target="_blank">
    <i class="bi bi-whatsapp"></i>
</a>

<script>
    function changeImg(src) { document.getElementById('mainView').src = src; }
</script>
<?php
include __DIR__ . '/../includes/footer.php';
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
require_once('../db_conn.php');
 include __DIR__ . '/../includes/header.php';
global $conn;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZELDASTORES | Premium Dropshipping Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container mb-5 mt-n5">
    <div class="row g-3 justify-content-center">
        <div class="col-6 col-lg-2">
            <a href="index.php?cat=Electronic" class="cat-pill">
                <i class="bi bi-laptop fs-2 text-primary"></i>
                <span class="mt-2 fw-bold">Electronic</span>
            </a>
        </div>
        <div class="col-6 col-lg-2">
            <a href="index.php?cat=Kitchen" class="cat-pill">
                <i class="bi bi-cup-hot fs-2 text-warning"></i>
                <span class="mt-2 fw-bold">Kitchen</span>
            </a>
        </div>
        <div class="col-6 col-lg-2">
            <a href="index.php?cat=Cream" class="cat-pill">
                <i class="bi bi-droplet fs-2 text-info"></i>
                <span class="mt-2 fw-bold">Beauty</span>
            </a>
        </div>
        <div class="col-6 col-lg-2">
            <a href="index.php?cat=Automotive" class="cat-pill">
                <i class="bi bi-car-front fs-2 text-danger"></i>
                <span class="mt-2 fw-bold">Automotive</span>
            </a>
        </div>
    </div>
</div>

<section class="container mb-5" id="shop">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="fw-bold fs-1">Trending <span class="text-primary">Now</span></h2>
        <?php if((isset($_GET['cat']) && !empty(trim($_GET['cat']))) || (isset($_GET['search']) && !empty(trim($_GET['search'])))): ?>
            <a href="index.php" class="btn btn-sm btn-outline-primary rounded-pill px-3">View All Products</a>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <?php
        if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
            $s = "%" . trim($_GET['search']) . "%";
            $stmt = $conn->prepare("SELECT * FROM products WHERE pro_name LIKE ? AND pro_status = 1 ORDER BY id DESC");
            $stmt->bind_param("s", $s);
        } elseif (isset($_GET['cat']) && !empty(trim($_GET['cat']))) {
            $cat = trim($_GET['cat']);
            $stmt = $conn->prepare("SELECT * FROM products WHERE pro_category = ? AND pro_status = 1 ORDER BY id DESC");
            $stmt->bind_param("s", $cat);
        } else {
            $stmt = $conn->prepare("SELECT * FROM products WHERE pro_status = 1 ORDER BY id DESC");
        }

        if ($stmt) {
            $stmt->execute();
            $res = $stmt->get_result();

            if ($res && $res->num_rows > 0) {
                while($row = $res->fetch_assoc()) {
                    $pro_price = $row['pro_price'] ?? 0;
                    $pro_old_price = $row['pro_old_price'] ?? 0;
                    $pro_name_clean = htmlspecialchars($row['pro_name'] ?? '');
                    
                    $wa_url = "https://wa.me/923044304440?text=" . urlencode("Hi ZeldaStores, I am interested in: " . $row['pro_name'] . " (Rs. " . number_format($pro_price) . ")");
            ?>
           <div class="col-6 col-md-4 col-lg-3 mb-4">
    <div class="product-card shadow-sm h-100 d-flex flex-column justify-content-between">
        <div class="img-container shadow-sm">
            <?php if(!empty($pro_old_price) && $pro_old_price > $pro_price): 
                $saving = $pro_old_price - $pro_price;
            ?>
                <span class="offer-tag">Save Rs. <?php echo number_format($saving); ?></span>
            <?php endif; ?>

            <img src="../image/<?php echo htmlspecialchars($row['pro_image'] ?? 'default.jpg'); ?>" alt="<?php echo $pro_name_clean; ?>">
        </div>
        
        <div class="px-2 d-flex flex-column flex-grow-1 justify-content-between mt-2">
            <div>
                <small class="text-uppercase text-muted"><?php echo htmlspecialchars($row['pro_category'] ?? ''); ?></small>
                
                <h5 class="fw-bold mt-1 mb-2 product-title-multiline" title="<?php echo $pro_name_clean; ?>">
                    <?php echo $pro_name_clean; ?>
                </h5>
                
                <div class="d-flex align-items-center gap-2 mb-3">
                    <div class="price-badge m-0">Rs. <?php echo number_format($pro_price); ?></div>
                    <?php if(!empty($pro_old_price) && $pro_old_price > 0): ?>
                        <span class="text-muted text-decoration-line-through small fw-semibold" style="font-size: 0.85rem; opacity: 0.7;">
                            Rs. <?php echo number_format((float)$pro_old_price); ?>
                        </span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mt-auto">
                <a href="product_details.php?id=<?php echo $row['id']; ?>" class="btn-view shadow-sm mb-2 d-block text-center">Details</a>
                <a href="<?php echo $wa_url; ?>" target="_blank" rel="noopener noreferrer" class="btn-wa d-block text-center text-decoration-none shadow-sm">
                    <i class="bi bi-whatsapp me-2"></i>Order via WA
                </a>
            </div>
        </div>
    </div>
</div>
            <?php 
                } 
            } else {
                echo "<div class='col-12 text-center py-5'><i class='bi bi-search fs-1 opacity-25'></i><h4 class='mt-3 opacity-50'>Sorry! No product found.</h4></div>";
            } 
            $stmt->close();
        } else {
            echo "<div class='col-12 text-center py-5'><h4 class='text-danger'>Database Connection Error!</h4></div>";
        }
        ?>
    </div>
</section>

<a href="https://wa.me/923044304440" class="wa-float shadow-lg" target="_blank" rel="noopener noreferrer">
    <i class="bi bi-whatsapp"></i>
</a>
<?php
include __DIR__ . '/../includes/footer.php';
?>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
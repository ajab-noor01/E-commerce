<?php
session_start();
include('../db_conn.php');
global $conn;

// 1. Security Check: Admin logged in hona lazmi hai
if(!isset($_SESSION['admin_logged_in'])) { 
    header("Location: login.php"); 
    exit(); 
}

// Active Filter Status (Redirection ke baad filter barqaraar rakhne ke liye)
$current_status = isset($_GET['status']) ? $_GET['status'] : '';

// 2. Action Engine: Status Updates Using Secure Prepared Statements
// Mark as Received
if (isset($_GET['mark_done'])) {
    $id = intval($_GET['mark_done']);
    
    $stmt = $conn->prepare("UPDATE orders SET status='Received' WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        $stmt->close();
        $redirect_url = "view_orders.php?msg=success" . ($current_status ? "&status=" . urlencode($current_status) : "");
        header("Location: " . $redirect_url);
        exit();
    }
}

// Mark as Returned
if (isset($_GET['mark_return'])) {
    $id = intval($_GET['mark_return']);
    
    $stmt = $conn->prepare("UPDATE orders SET status='Returned' WHERE id=?");
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        $stmt->close();
        $redirect_url = "view_orders.php?msg=returned" . ($current_status ? "&status=" . urlencode($current_status) : "");
        header("Location: " . $redirect_url);
        exit();
    }
}

// 3. Fetch Filtered Orders with Product Details Safely
if (!empty($current_status)) {
    // Agar status URL mein majood hai (Pending / Received / Returned)
    $stmt = $conn->prepare("SELECT orders.*, products.pro_name, products.pro_price 
                            FROM orders 
                            INNER JOIN products ON orders.product_id = products.id 
                            WHERE orders.status = ? 
                            ORDER BY orders.id DESC");
    $stmt->bind_param("s", $current_status);
    $stmt->execute();
    $res = $stmt->get_result();
} else {
    // Agar koi filter nahi laga toh saare orders fetch honge
    $query = "SELECT orders.*, products.pro_name, products.pro_price 
              FROM orders 
              INNER JOIN products ON orders.product_id = products.id 
              ORDER BY orders.id DESC";
    $res = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management | ZeldaStores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container-fluid py-5 px-md-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold m-0">Orders Center</h2>
            <p class="text-muted small">Manage shipments and customer deliveries</p>
        </div>
        <a href="dashboard.php" class="btn btn-outline-dark rounded-pill px-4 fw-bold">Dashboard</a>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'success'): ?>
        <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4">Order successfully marked as Received and paid!</div>
    <?php endif; ?>
    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'returned'): ?>
        <div class="alert alert-warning border-0 rounded-4 shadow-sm mb-4">Order inventory marked as Returned (RTO).</div>
    <?php endif; ?>

    <div class="main-card border">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>ID & Date</th>
                        <th>Customer Info</th>
                        <th>Shipping Address</th>
                        <th>Product Details</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(mysqli_num_rows($res) > 0): ?>
                        <?php while($row = mysqli_fetch_assoc($res)): ?>
                        <tr>
                            <td>
                                <div class="fw-bold">#<?php echo $row['id']; ?></div>
                                <div class="text-muted" style="font-size: 10px;">
                                    <i class="bi bi-calendar3"></i> <?php echo date('d M, Y', strtotime($row['created_at'])); ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-navy"><?php echo htmlspecialchars($row['c_name']); ?></div>
                                <div class="small text-primary fw-semibold"><i class="bi bi-telephone"></i> <?php echo htmlspecialchars($row['c_phone']); ?></div>
                            </td>
                            <td>
                                <div class="customer-address">
                                    <i class="bi bi-geo-alt-fill text-danger small"></i> 
                                    <?php echo (!empty($row['c_address'])) ? htmlspecialchars($row['c_address']) : 'No Address Provided'; ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold"><?php echo htmlspecialchars($row['pro_name']); ?></div>
                                <div class="price-text">
                                    Rs. <?php 
                                        $final_p = ($row['total_price'] > 0) ? $row['total_price'] : $row['pro_price'];
                                        echo number_format($final_p); 
                                    ?>
                                </div>
                            </td>
                            <td>
                                <?php if($row['status'] == 'Pending'): ?>
                                    <span class="badge-pending">PENDING</span>
                                <?php elseif($row['status'] == 'Returned'): ?>
                                    <span class="badge-returned">RETURNED</span>
                                <?php else: ?>
                                    <span class="badge-received">RECEIVED</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end">
                                <?php if($row['status'] == 'Pending'): ?>
                                    <a href="view_orders.php?mark_done=<?php echo $row['id']; ?>" 
                                       class="btn-action" 
                                       onclick="return confirm('Confirm payment received?')">
                                         Mark Received
                                    </a>
                                    <a href="view_orders.php?mark_return=<?php echo $row['id']; ?>" 
                                       class="btn-return" 
                                       onclick="return confirm('Mark this as Returned?')">
                                         Return Order
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted fw-bold small"><i class="bi bi-lock-fill"></i> Completed</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5 text-muted">No orders yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
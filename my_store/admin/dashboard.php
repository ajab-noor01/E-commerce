<?php
session_start();
// Database Connection Link
include('../db_conn.php');

// --- 1. SECURITY LAYER ---
if(!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

mysqli_report(MYSQLI_REPORT_OFF);

// --- 2. DYNAMIC FILTER ENGINE ---
// Check karein ke user ne kaunsa saal select kiya hai, default current year hoga
$selected_year = isset($_GET['year']) ? intval($_GET['year']) : intval(date('Y'));
$current_year = intval(date('Y'));
$current_month = date('m');
$today_day = intval(date('j'));

// Category Filter Get Karein
$selected_category = isset($_GET['category']) ? trim($_GET['category']) : '';

// --- 3. ANALYTICS ENGINE (SQL LOGIC) ---

// A. Products Inventory Count (Filtered or Total)
if (!empty($selected_category)) {
    $stmt_cat_count = $conn->prepare("SELECT id FROM products WHERE pro_category = ?");
    $stmt_cat_count->bind_param("s", $selected_category);
    $stmt_cat_count->execute();
    $res_total = $stmt_cat_count->get_result();
} else {
    $res_total = mysqli_query($conn, "SELECT id FROM products");
}
$total_products = ($res_total) ? mysqli_num_rows($res_total) : 0;

// B. Financial Revenue Calculation (Total Sale PKR)
$res_sales = mysqli_query($conn, "
    SELECT SUM(IF(orders.total_price > 0, orders.total_price, products.pro_price)) as total_revenue 
    FROM orders 
    INNER JOIN products ON orders.product_id = products.id 
    WHERE orders.status='Received'
");
$row_sales = mysqli_fetch_assoc($res_sales);
$total_revenue = ($row_sales['total_revenue']) ? $row_sales['total_revenue'] : 0;

// C. Operational Metrics: Pending Orders
$res_pending = mysqli_query($conn, "SELECT orders.id FROM orders INNER JOIN products ON orders.product_id = products.id WHERE orders.status='Pending'");
$pending_count = ($res_pending) ? mysqli_num_rows($res_pending) : 0;

// D. Logistics Metrics: Received Orders Count
$res_received = mysqli_query($conn, "SELECT orders.id FROM orders INNER JOIN products ON orders.product_id = products.id WHERE orders.status='Received'");
$received_count = ($res_received) ? mysqli_num_rows($res_received) : 0;

// E. Returned orders ki counting
$res_returned = mysqli_query($conn, "SELECT orders.id FROM orders INNER JOIN products ON orders.product_id = products.id WHERE orders.status='Returned'");
$returned_count = ($res_returned) ? mysqli_num_rows($res_returned) : 0;

// F. Unique Customers counting based on phone numbers
$res_users = mysqli_query($conn, "SELECT DISTINCT orders.c_phone FROM orders INNER JOIN products ON orders.product_id = products.id");
$customer_count = ($res_users) ? mysqli_num_rows($res_users) : 0;

// G. Category Wise Product Query (Dashboard Table / List Rendering ke Liye)
if (!empty($selected_category)) {
    $stmt_p = $conn->prepare("SELECT * FROM products WHERE pro_category = ? ORDER BY id DESC");
    $stmt_p->bind_param("s", $selected_category);
    $stmt_p->execute();
    $res_products = $stmt_p->get_result();
} else {
    $res_products = mysqli_query($conn, "SELECT * FROM products ORDER BY id DESC");
}


// --- 4. ADVANCED GRAPH ENGINE (SHOPIFY STYLE) ---
$graph_labels = [];
$graph_values = [];
$chart_title = "";

if ($selected_year === $current_year) {
    // SCENARIO 1: Agar CURRENT YEAR select hua hai -> Daily Tracking dikhao (Sirf aaj tak)
    $chart_title = "Current Month Live Tracking";
    $total_days = date('t'); // Is mahine ke total din (31)

    // Pehle pure mahine ka data database se grouped nikalte hain execution speed ke liye
    $monthly_db_data = [];
    $res_graph = mysqli_query($conn, "
        SELECT DAY(orders.created_at) as order_day, 
               SUM(IF(orders.total_price > 0, orders.total_price, products.pro_price)) as daily_revenue 
        FROM orders 
        INNER JOIN products ON orders.product_id = products.id 
        WHERE orders.status='Received' 
          AND MONTH(orders.created_at) = '$current_month' 
          AND YEAR(orders.created_at) = '$selected_year'
        GROUP BY DAY(orders.created_at)
    ");
    
    if ($res_graph) {
        while($rg = mysqli_fetch_assoc($res_graph)) {
            $monthly_db_data[$rg['order_day']] = (int)$rg['daily_revenue'];
        }
    }

    // Loop jo future ke dino ko null karega taake line hawa mein na latke
    for ($i = 1; $i <= $total_days; $i++) {
        $graph_labels[] = $i; // Days (1, 2, 3...)
        
        if ($i <= $today_day) {
            $graph_values[] = isset($monthly_db_data[$i]) ? $monthly_db_data[$i] : 0;
        } else {
            $graph_values[] = null; // Future days are strictly NULL -> Graph cuts off here!
        }
    }
} else {
    // SCENARIO 2: Agar PICHLA SAAL select hua hai -> Monthly Overview (Jan - Dec) dikhao
    $chart_title = "Yearly Sales Performance Overview ($selected_year)";
    $graph_labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    
    $yearly_db_data = array_fill(1, 12, 0);
    $res_year_graph = mysqli_query($conn, "
        SELECT MONTH(orders.created_at) as order_month, 
               SUM(IF(orders.total_price > 0, orders.total_price, products.pro_price)) as monthly_revenue 
        FROM orders 
        INNER JOIN products ON orders.product_id = products.id 
        WHERE orders.status='Received' 
          AND YEAR(orders.created_at) = '$selected_year'
        GROUP BY MONTH(orders.created_at)
    ");

    if ($res_year_graph) {
        while($ry = mysqli_fetch_assoc($res_year_graph)) {
            $yearly_db_data[$ry['order_month']] = (int)$ry['monthly_revenue'];
        }
    }
    $graph_values = array_values($yearly_db_data);
}

$js_labels = json_encode($graph_labels);
$js_values = json_encode($graph_values);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZeldaStores | Admin Management System</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container py-5">
    
    <!-- Header Section -->
    <nav class="glass-header d-flex flex-column flex-md-row justify-content-between align-items-center mb-4 p-3 rounded-4 bg-white shadow-sm">
        <div class="header-info mb-3 mb-md-0">
            <div class="brand-text fs-4 fw-bold">Zelda<span class="text-primary">Stores</span> Admin</div>
            <p class="text-muted m-0 small fw-bold">
                <i class="bi bi-cpu-fill text-primary me-1"></i> Engine Status: <span class="text-success">Operational</span>
            </p>
        </div>
        
        <div class="header-actions d-flex flex-wrap gap-2 align-items-center">
            <!-- Unified Single Filter Form (Year + Category) -->
            <form method="GET" action="dashboard.php" class="d-flex gap-2 m-0">
                <!-- Year Filter -->
                <select name="year" class="year-filter shadow-sm form-select text-center" onchange="this.form.submit()" style="cursor: pointer;">
                    <option value="2026" <?php if($selected_year === 2026) echo 'selected'; ?>>Year: 2026</option>
                    <option value="2025" <?php if($selected_year === 2025) echo 'selected'; ?>>Year: 2025</option>
                </select>

                <!-- Category Filter -->
                <select name="category" class="form-select shadow-sm" onchange="this.form.submit()" style="cursor: pointer;">
                    <option value="">All Categories</option>
                    <option value="Electronic" <?php if(isset($selected_category) && $selected_category == 'Electronic') echo 'selected'; ?>>Electronic</option>
                    <option value="Kitchen" <?php if(isset($selected_category) && $selected_category == 'Kitchen') echo 'selected'; ?>>Kitchen</option>
                    <option value="Beauty" <?php if(isset($selected_category) && $selected_category == 'Beauty') echo 'selected'; ?>>Beauty</option>
                    <option value="Auto" <?php if(isset($selected_category) && $selected_category == 'Auto') echo 'selected'; ?>>Auto</option>
                    <option value="General" <?php if(isset($selected_category) && $selected_category == 'General') echo 'selected'; ?>>General</option>
                </select>
            </form>

            <a href="logout.php" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold ms-2">
                <i class="bi bi-power me-1"></i> Logout
            </a>
        </div>
    </nav>

    <!-- Stat Cards Rows -->
    <div class="row g-2 g-md-4 mb-5"> 
        <!-- Products Card -->
        <div class="col-6 col-md-4 col-lg-2">
            <a href="view_products.php" class="card-stat text-decoration-none text-white d-block rounded-4 h-100" style="background: var(--gradient-1, #4e73df); padding: 20px;">
                <div class="stat-icon" style="width: 45px; height: 45px; font-size: 20px; margin-bottom: 15px;">
                    <i class="bi bi-collection"></i>
                </div>
                <h2 class="fw-bold m-0" style="font-size: 1.4rem;"><?php echo $total_products; ?></h2>
                <div class="text-white-50 fw-bold text-uppercase mt-1" style="font-size: 10px; letter-spacing: 0.5px;">Products</div>
            </a>
        </div>

        <!-- Revenue Card -->
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card-stat text-white rounded-4 h-100" style="background: var(--gradient-warning, #f6c23e); padding: 20px;">
                <div class="stat-icon" style="width: 45px; height: 45px; font-size: 20px; margin-bottom: 15px;">
                    <i class="bi bi-wallet2"></i>
                </div>
                <h2 class="fw-bold m-0" style="font-size: 1.1rem;">Rs. <?php echo number_format($total_revenue); ?></h2>
                <div class="text-white-50 fw-bold text-uppercase mt-1" style="font-size: 10px; letter-spacing: 0.5px;">Total Sale</div>
            </div>
        </div>

        <!-- Pending Orders -->
        <div class="col-4 col-md-4 col-lg-2">
            <a href="view_orders.php?status=Pending" class="card-stat text-decoration-none text-white d-block rounded-4 h-100" style="background: var(--gradient-danger, #e74a3b); padding: 20px;">
                <div class="stat-icon" style="width: 45px; height: 45px; font-size: 20px; margin-bottom: 15px;">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h2 class="fw-bold m-0" style="font-size: 1.4rem;"><?php echo $pending_count; ?></h2>
                <div class="text-white-50 fw-bold text-uppercase mt-1" style="font-size: 10px; letter-spacing: 0.5px;">Pending</div>
            </a>
        </div>

        <!-- Received Orders -->
        <div class="col-4 col-md-6 col-lg-2">
            <a href="view_orders.php?status=Received" class="card-stat text-decoration-none text-white d-block rounded-4 h-100" style="background: var(--gradient-success, #1cc88a); padding: 20px;">
                <div class="stat-icon" style="width: 45px; height: 45px; font-size: 20px; margin-bottom: 15px;">
                    <i class="bi bi-patch-check"></i>
                </div>
                <h2 class="fw-bold m-0" style="font-size: 1.4rem;"><?php echo $received_count; ?></h2>
                <div class="text-white-50 fw-bold text-uppercase mt-1" style="font-size: 10px; letter-spacing: 0.5px;">Received</div>
            </a>
        </div>

        <!-- Returned Orders -->
        <div class="col-4 col-md-6 col-lg-3">
            <a href="view_orders.php?status=Returned" class="card-stat text-decoration-none text-white d-block rounded-4 h-100" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%); padding: 20px;">
                <div class="stat-icon" style="width: 45px; height: 45px; font-size: 20px; margin-bottom: 15px;">
                    <i class="bi bi-arrow-left-right"></i>
                </div>
                <h2 class="fw-bold m-0" style="font-size: 1.4rem;"><?php echo $returned_count; ?></h2>
                <div class="text-white-50 fw-bold text-uppercase mt-1" style="font-size: 10px; letter-spacing: 0.5px;">Returned</div>
            </a>
        </div>
    </div>

    <!-- SHOPIFY STYLE DYNAMIC SALES GRAPH -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="control-panel p-4 bg-white rounded-4 shadow-sm border">
                <div class="d-flex justify-content-between align-items-center mb-4 px-2">
                    <div>
                        <h4 class="fw-bold m-0 text-navy-dark">Sales Performance</h4>
                        <small class="text-muted fw-bold"><?php echo $chart_title; ?></small>
                    </div>
                    <div class="badge px-3 py-2 rounded-pill fw-bold" style="background-color: rgba(67, 24, 255, 0.1); color: var(--primary-blue, #4e73df);">
                        <i class="bi bi-graph-up-arrow me-1"></i> PKR Trend
                    </div>
                </div>
                <div style="position: relative; height:320px; width:100%;">
                    <canvas id="shopifySalesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- FILTERED PRODUCTS TABLE SECTION -->
    <?php if(!empty($selected_category)): ?>
    <div class="row mb-5">
        <div class="col-12">
            <div class="control-panel p-4 bg-white rounded-4 shadow-sm border">
                <h5 class="fw-bold mb-3">
                    Category: <span class="text-primary"><?php echo htmlspecialchars($selected_category); ?></span>
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Price</th>
                                <th>Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(isset($res_products) && mysqli_num_rows($res_products) > 0): ?>
                                <?php while($row = mysqli_fetch_assoc($res_products)): ?>
                                    <tr>
                                        <td class="fw-semibold"><?php echo htmlspecialchars($row['pro_name']); ?></td>
                                        <td><span class="badge bg-secondary"><?php echo htmlspecialchars($row['pro_category']); ?></span></td>
                                        <td>PKR <?php echo number_format($row['pro_price'], 2); ?></td>
                                        <td><?php echo $row['product_stock']; ?> units</td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-3">No product this categorie</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Core Tasks and Summary Section -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="control-panel p-4 bg-white rounded-4 shadow-sm border">
                <h4 class="fw-bold mb-4">Core Management Tasks</h4>
                
                <a href="add_product.php" class="action-link text-decoration-none d-flex align-items-center p-3 rounded-3 mb-3 shadow-sm" style="background: var(--navy-dark, #1a202c); color: white;">
                    <i class="bi bi-plus-circle-fill me-3 fs-3" style="background: rgba(255,255,255,0.1); padding: 10px; rounded: 50%;"></i>
                    <div>
                        <span class="fs-5 d-block fw-bold">Add New Product</span>
                        <small class="opacity-75">Upload stock items with high-res images</small>
                    </div>
                </a>

                <a href="view_products.php" class="action-link text-decoration-none d-flex align-items-center p-3 rounded-3 mb-3 border shadow-sm text-dark">
                    <i class="bi bi-pencil-square me-3 fs-3 text-primary"></i>
                    <div>
                        <span class="fs-5 d-block fw-bold">Inventory & Stock Manager</span>
                        <small class="text-muted">Modify existing listings or update prices</small>
                    </div>
                </a>

                <a href="view_orders.php" class="action-link text-decoration-none d-flex align-items-center p-3 rounded-3 border shadow-sm text-dark">
                    <i class="bi bi-truck me-3 fs-3 text-success"></i>
                    <div>
                        <span class="fs-5 d-block fw-bold">Order Processing Center</span>
                        <small class="text-muted">Verify shipments and update customer statuses</small>
                    </div>
                </a>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="control-panel text-center p-4 bg-white rounded-4 shadow-sm border">
                <div class="mb-4">
                    <i class="bi bi-bar-chart-line-fill display-4 text-primary opacity-25"></i>
                </div>
                <h5 class="fw-bold">Real-time Summary</h5>
                <p class="text-muted small px-3">
                    Serving <span class="text-primary fw-bold"><?php echo $customer_count; ?></span> unique customers across all shipping addresses.
                </p>
                
                <hr class="my-4 opacity-10">
                
                <a href="../frontend/index.php" target="_blank" class="btn btn-dark w-100 py-3 rounded-pill fw-bold mb-3 shadow">
                    <i class="bi bi-eye me-2"></i> Launch Storefront
                </a>
                
                <div class="bg-light p-3 rounded-4 border border-dashed">
                    <span class="text-muted small fw-bold">SERVER SYNC: OK</span>
                </div>
            </div>
        </div> 
    </div>

</div>
    </div>

    <footer class="mt-5 pt-4 text-center">
        <p class="text-muted small">ZeldaStores Logistics v2.7 | &copy; 2026 Admin Panel</p>
        <div class="d-flex justify-content-center gap-2">
            <span class="badge bg-secondary opacity-50">PHP 8.2</span>
            <span class="badge bg-secondary opacity-50">Bootstrap 5.3</span>
        </div>
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById('shopifySalesChart').getContext('2d');
    
    const salesGradient = ctx.createLinearGradient(0, 0, 0, 300);
    salesGradient.addColorStop(0, 'rgba(67, 24, 255, 0.3)');
    salesGradient.addColorStop(1, 'rgba(67, 24, 255, 0.0)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo $js_labels; ?>,
            datasets: [{
                label: 'Revenue',
                data: <?php echo $js_values; ?>,
                borderColor: '#4318FF',
                borderWidth: 3,
                pointBackgroundColor: '#4318FF',
                pointHoverRadius: 7,
                fill: true,
                backgroundColor: salesGradient,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { display: false },
                    title: { 
                        display: true, 
                        text: '<?php echo ($selected_year === $current_year) ? "Days of the Month" : "Months of the Year"; ?>', 
                        font: { weight: 'bold' } 
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0, 0, 0, 0.03)' },
                    ticks: {
                        callback: function(value) { return 'Rs. ' + value.toLocaleString(); }
                    }
                }
            }
        }
    });
});
</script>
</body>
</html>
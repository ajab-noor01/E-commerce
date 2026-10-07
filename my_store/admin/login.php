<?php
session_start();
include('../db_conn.php');
global $conn;
// 1. SECURITY: Agar pehle se login hai toh seedha dashboard par bhej do
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

$error = "";

if (isset($_POST['login_btn'])) {
    $user = $_POST['my_admin_user'];
    $pass = $_POST['my_admin_pass'];

    // 2. SECURE SQL: Prepared Statements ka istemal (Hack-proof layer)
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $admin_data = $result->fetch_assoc();
        
        // 3. PASSWORD CHECK: Agar aap plain text use kar rahe hain toh direct check (Future mein password_verify use karna chahiye)
       // 🔒 Nayi Secure Line (Password Hash Verification):
if (password_verify($pass, $admin_data['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $admin_data['username'];
            
            // Seedha admin dashboard par bhejein
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Ghalat Username ya Password!";
        }
    } else {
        $error = "Ghalat Username ya Password!";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Authentication | ZeldaStores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<div class="container login-container">
    <div class="login-card">
        <div class="text-center mb-4">
            <h2 class="fw-bold m-0 text-uppercase" style="letter-spacing: -1px;">Zelda<span class="text-primary">Stores</span></h2>
            <p class="text-muted small fw-bold mt-1"><i class="bi bi-shield-lock-fill text-primary"></i> Control Panel Secure Access</p>
        </div>
        
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger border-0 rounded-4 shadow-sm text-center small fw-semibold py-2"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" autocomplete="off">
            <div class="mb-3">
                <label class="form-label small fw-bold">Admin Username</label>
                <div class="position-relative">
                    <input type="text" name="my_admin_user" class="form-control" placeholder="Enter username" autocomplete="new-password" required>
                </div>
            </div>
            
            <div class="mb-4">
                <label class="form-label small fw-bold">Secure Password</label>
                <input type="password" name="my_admin_pass" class="form-control" placeholder="••••••••" autocomplete="new-password" required>
            </div>
            
            <button type="submit" name="login_btn" class="btn btn-primary w-100 shadow-sm">Sign In</button>
        </form>
        
        <div class="text-center mt-4">
            <a href="../frontend/index.php" class="text-decoration-none small text-muted fw-bold"><i class="bi bi-arrow-left"></i> Back to Storefront</a>
        </div>
    </div>
</div>

</body>
</html>
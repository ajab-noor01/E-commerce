<nav class="navbar navbar-expand-lg sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php" style="color: var(--dark-bg);">
            <i class="bi bi-lightning-charge-fill text-primary"></i> ZELDA<span class="text-primary">STORES</span>
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <form action="index.php" method="GET" class="position-relative d-block d-lg-none mt-3 mb-2">
                <input type="text" name="search" class="form-control rounded-pill border-0 ps-4 bg-light" placeholder="Search products...">
                <button type="submit" class="btn position-absolute end-0 top-0 text-primary"><i class="bi bi-search"></i></button>
            </form>

            <ul class="navbar-nav mx-auto align-items-center">
                <li class="nav-item"><a class="nav-link px-3 active" href="index.php">Home</a></li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle px-3" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Shop</a>
                    <ul class="dropdown-menu shadow-lg border-0 rounded-4">
                        <li><a class="dropdown-item" href="index.php?cat=Electronic">Electronics</a></li>
                        <li><a class="dropdown-item" href="index.php?cat=Kitchen">Kitchen Gear</a></li>
                        <li><a class="dropdown-item" href="index.php?cat=Cream">Beauty</a></li>
                        <li><a class="dropdown-item" href="index.php?cat=Automotive">Automotive</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a class="nav-link px-3" href="#support">Support</a></li>
                
                <?php if(isset($_SESSION['admin_logged_in'])): ?>
                <li class="nav-item">
                    <a class="btn btn-primary text-white px-4 rounded-pill ms-lg-3 shadow-sm" href="../admin/dashboard.php">
                        <i class="bi bi-grid-fill me-1"></i> Dashboard
                    </a>
                </li>
                <?php endif; ?>
            </ul>

            <div class="d-flex align-items-center gap-3 mt-3 mt-lg-0 justify-content-center">
                <form action="index.php" method="GET" class="position-relative d-none d-lg-block">
                    <input type="text" name="search" class="form-control rounded-pill border-0 ps-4 bg-light" placeholder="Search..." style="width: 140px;">
                    <button type="submit" class="btn position-absolute end-0 top-0 text-primary"><i class="bi bi-search"></i></button>
                </form>

                <?php if(isset($_SESSION['admin_logged_in'])): ?>
                    <a href="../admin/logout.php" class="btn btn-danger text-white px-4 rounded-pill shadow-sm">
                        <i class="bi bi-box-arrow-right"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="../admin/login.php" class="social-circle shadow-sm" title="Admin Login">
                        <i class="bi bi-person-fill-lock"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<header class="hero-section text-center">
    <div class="container">
       <span class="badge rounded-pill bg-danger px-3 py-2 mb-3 shadow-sm fw-bold" style="position: relative; top: -25px !important;">🔥 MEGA SALE: UP TO 40% OFF + CASH ON DELIVERY</span>
        <h1 class="display-3 fw-bold mb-4">Elevate Your Lifestyle<br>With Premium Deals</h1>
        <p class="lead opacity-75 mb-5 mx-auto" style="max-width: 600px;">Discover handcrafted quality products delivered directly to your doorstep with speed and care.</p>
        <div class="d-flex justify-content-center gap-3">
            <a href="#shop" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">Shop Now</a>
        </div>
    </div>
</header>
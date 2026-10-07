<?php 
session_start();
include('../db_conn.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return & Exchange Policy |ZeldaStores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fw-bold fs-3" href="index.php" style="color: var(--dark-bg);">
            <i class="bi bi-lightning-charge-fill text-primary"></i> ZELDA<span class="text-primary">STORES</span>
        </a>

        <div class="d-flex align-items-center gap-3 ms-auto">
            <a href="index.php" class="btn btn-outline-primary rounded-pill px-4 shadow-sm fw-semibold">
                <i class="bi bi-arrow-left me-2"></i>Back to Shop
            </a>
        </div>
    </div>
</nav>

<header class="policy-hero text-center">
    <div class="container">
        <h1 class="display-4 fw-bold mb-3">Return & Exchange Policy</h1>
        <p class="lead opacity-75 mx-auto" style="max-width: 600px;">Your trust is our priority. Read our hassle-free replacement guidelines.</p>
    </div>
</header>

<main class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="policy-card">
                
                <div class="mb-5">
                    <h3 class="section-title"><i class="bi bi-shield-check"></i> 1. Exchange Policy Overview</h3>
                    <p class="text-muted lh-lg">At ZeldaStores, we strive to deliver premium quality products. To protect our business and ensure fair trade, we offer a <b>7-Day Replacement Policy</b> only if the product received is damaged, defective, or incorrect. We do not offer cash refunds.</p>
                </div>

                <div class="mb-5">
                    <h3 class="section-title"><i class="bi bi-exclamation-triangle"></i> 2. Mandatory Eligibility Criteria</h3>
                    <p class="text-muted mb-3">To claim a replacement, the following strict guidelines must be met:</p>
                    <ul class="policy-list">
                        <li><strong>Parcel Opening Video:</strong> Customers must record a clear video while unboxing/opening the courier parcel. Claims without a valid unboxing video will not be entertained.</li>
                        <li><strong>Unused Condition:</strong> The item must be unused, unaltered, and in the same condition as received.</li>
                        <li><strong>Original Packaging:</strong> The product must be returned with its original brand box, tags, and all accessories intact.</li>
                        <li><strong>No Change of Mind:</strong> We do not accept returns or exchanges due to a customer's change of mind or personal preference after receiving the product.</li>
                    </ul>
                </div>

                <div class="mb-4">
                    <h3 class="section-title"><i class="bi bi-gear"></i> 3. How to Request a Replacement</h3>
                    <p class="text-muted mb-3">If you received a faulty product, follow these simple steps within 7 days of delivery:</p>
                    <ol class="policy-list" type="A">
                        <li>Send us a message on our official WhatsApp Support (<b>+92 304 4304440</b>).</li>
                        <li>Provide your Order ID, clear pictures of the item, and the <strong>Mandatory Unboxing Video</strong>.</li>
                        <li>Our quality assurance team will review your claim within 24-48 hours. Once approved, a fresh replacement piece will be dispatched to your address.</li>
                    </ol>
                </div>

                <div class="alert alert-warning rounded-4 border-0 p-4 m-0 mt-4 shadow-sm">
                    <h5 class="fw-bold text-dark"><i class="bi bi-info-circle-fill me-2"></i> Please Note:</h5>
                    <p class="small text-secondary m-0 lh-lg">Reverse shipping/courier charges for returning the product to our warehouse are to be borne by the customer. Delivery charges are non-refundable under any circumstances.</p>
                </div>

            </div>
        </div>
    </div>
</main>

<footer class="text-center py-4 border-top bg-white text-muted small">
    &copy; 2026 ZeldaStores Official. All Rights Reserved.
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
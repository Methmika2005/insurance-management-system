<?php
session_start();
require_once 'db.php';

// Check if customer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Insurance Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(180deg, rgba(224, 242, 254, 0.35) 0%, rgba(240, 253, 244, 0.50) 100%), 
                        url('https://images.unsplash.com/photo-1600585154340-be6161a56a0c?q=80&w=1920&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            min-height: 100vh;
            color: #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .card-custom {
            border-radius: 20px;
            border: none;
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            max-width: 500px;
            width: 100%;
        }
        .success-icon {
            width: 80px;
            height: 80px;
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 36px;
            margin: 0 auto 20px auto;
        }
    </style>
</head>
<body>

    <div class="container text-center">
        <div class="card card-custom p-5 mx-auto">
            <div class="success-icon">
                <i class="fa-solid fa-check"></i>
            </div>
            <h2 class="fw-bold text-dark mb-2">Payment Successful!</h2>
            <p class="text-muted small mb-4">
                Thank you! Your insurance policy premium payment has been successfully processed through PayHere.
            </p>
            <div class="d-grid gap-2">
                <a href="customer_dashboard.php" class="btn btn-success rounded-pill py-2 fw-semibold shadow-sm">
                    <i class="fa-solid fa-house me-2"></i> Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
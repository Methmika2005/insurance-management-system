<?php
session_start();
require_once 'db.php';

// Check if customer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - Insurance Portal</title>
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
        }
        .navbar-custom {
            background: linear-gradient(90deg, #0284c7 0%, #2563eb 100%);
            padding: 15px 0;
            backdrop-filter: blur(8px);
        }
        .hero-banner {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        .card-custom {
            border-radius: 20px;
            border: none;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            background: rgba(255, 255, 255, 0.95);
        }
        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
        }
        .btn-custom-blue {
            background-color: #0284c7;
            color: white;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 500;
            border: none;
            transition: background 0.2s;
        }
        .btn-custom-blue:hover {
            background-color: #0369a1;
            color: white;
        }
        .btn-custom-green {
            background-color: #10b981;
            color: white;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 500;
            border: none;
            transition: background 0.2s;
        }
        .btn-custom-green:hover {
            background-color: #059669;
            color: white;
        }
        .btn-custom-yellow {
            background-color: #f59e0b;
            color: white;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 500;
            border: none;
            transition: background 0.2s;
        }
        .btn-custom-yellow:hover {
            background-color: #d97706;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="#">
                <i class="fa-solid fa-shield-halved me-2"></i> Insurance Portal
            </a>
            <div class="d-flex align-items-center">
                <a href="#" class="text-white position-relative me-4 fs-5 text-decoration-none">
                    <i class="fa-regular fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </a>
                <div class="d-flex align-items-center text-white bg-white bg-opacity-10 px-3 py-1.5 rounded-pill border border-white border-opacity-25">
                    <i class="fa-solid fa-circle-user fs-4 me-2"></i>
                    <div class="text-start me-2" style="line-height: 1.2;">
                        <small class="d-block text-white-50" style="font-size: 11px;">Welcome,</small>
                        <span class="fw-semibold" style="font-size: 13px;"><?php echo htmlspecialchars($user['full_name']); ?></span>
                    </div>
                </div>
                <a href="logout.php" class="btn btn-light btn-sm fw-semibold text-danger ms-3 rounded-pill px-3 shadow-sm">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content Container -->
    <div class="container py-4">
        
        <!-- Welcome Hero Banner -->
        <div class="hero-banner p-4 p-md-5 mb-4 position-relative overflow-hidden">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <span class="text-uppercase fw-bold text-primary tracking-wider" style="font-size: 12px; letter-spacing: 1px;">Welcome Back</span>
                    <h1 class="display-5 fw-bold text-dark mt-1 mb-3">Customer Dashboard</h1>
                    <p class="text-muted mb-0" style="max-width: 600px;">
                        You have successfully logged into the Insurance Management System using secure OTP verification. From here you can manage your policies, submit claims, and make payments.
                    </p>
                </div>
                <div class="col-lg-4 text-center d-none d-lg-block">
                    <div class="p-3 bg-primary bg-opacity-10 rounded-circle d-inline-block">
                        <i class="fa-solid fa-people-roof text-primary" style="font-size: 90px;"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Cards Grid -->
        <div class="row g-4">
            <!-- Card 1: My Policies -->
            <div class="col-md-4">
                <div class="card card-custom h-100 p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">My Policies</h4>
                        <p class="text-muted small mb-4">View your active insurance plans and status.</p>
                    </div>
                    <div>
                        <a href="#" class="btn btn-custom-blue w-100 d-flex align-items-center justify-content-center">
                            <span>View Policies</span> <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 2: Buy / Pay Insurance (LINKED TO MAKE PAYMENT) -->
            <div class="col-md-4">
                <div class="card card-custom h-100 p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-success bg-opacity-10 text-success">
                                <i class="fa-solid fa-credit-card"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Buy / Pay Insurance</h4>
                        <p class="text-muted small mb-4">Make secure payments via PayHere gateway.</p>
                    </div>
                    <div>
                        <a href="make_payment.php" class="btn btn-custom-green w-100 d-flex align-items-center justify-content-center">
                            <span>Make Payment</span> <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 3: Claims -->
            <div class="col-md-4">
                <div class="card card-custom h-100 p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-warning bg-opacity-10 text-warning">
                                <i class="fa-solid fa-file-circle-exclamation"></i>
                            </div>
                        </div>
                        <h4 class="fw-bold text-dark mb-2">Claims</h4>
                        <p class="text-muted small mb-4">Submit and track your insurance claim requests.</p>
                    </div>
                    <div>
                        <a href="#" class="btn btn-custom-yellow w-100 d-flex align-items-center justify-content-center text-white">
                            <span>Manage Claims</span> <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
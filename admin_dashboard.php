<?php
session_start();
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$admin_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Insurance Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, rgba(234, 242, 253, 0.45) 0%, rgba(255, 255, 255, 0.25) 100%), 
                        url('bg.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #1e293b;
            min-height: 100vh;
        }
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, #0b1a30 0%, #0d2342 100%);
            color: #fff;
            padding: 20px;
            z-index: 100;
        }
        .sidebar-brand {
            font-size: 1.25rem;
            font-weight: 700;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 35px;
            text-decoration: none;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li {
            margin-bottom: 8px;
        }
        .sidebar-menu a {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 12px 18px;
            color: #94a3b8;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .sidebar-menu a:hover, .sidebar-menu a.active {
            background: #2563eb;
            color: #fff;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 25px;
        }
        .top-navbar {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 16px;
            padding: 12px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            padding: 25px;
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <a href="admin_dashboard.php" class="sidebar-brand">
            <i class="fa-solid fa-shield-halved text-primary fs-4"></i> Insurance Portal
        </a>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="manage_users.php"><i class="fa-solid fa-users"></i> Customers</a></li>
            <li><a href="manage_packages.php"><i class="fa-solid fa-file-shield"></i> Policies</a></li>
            <li><a href="manage_claims.php"><i class="fa-solid fa-file-invoice-dollar"></i> Claims</a></li>
            <li><a href="payments.php"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
            <li><a href="#"><i class="fa-solid fa-chart-pie"></i> Reports</a></li>
            <li><a href="#"><i class="fa-solid fa-gear"></i> Settings</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Top Navbar -->
        <div class="top-navbar">
            <span class="text-muted small fw-semibold">Dashboard <i class="fa-solid fa-chevron-right mx-1"></i> Admin Overview</span>
            <div class="d-flex align-items-center gap-3">
                <span class="fw-semibold small">Admin: <?php echo htmlspecialchars($admin_name); ?></span>
                <a href="logout.php" class="btn btn-danger btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                </a>
            </div>
        </div>

        <!-- Glass Card Container -->
        <div class="glass-card">
            <div class="mb-4">
                <h3 class="fw-bold mb-1">Welcome back, <?php echo htmlspecialchars($admin_name); ?>!</h3>
                <p class="text-muted small mb-0">Here is an overview of the insurance portal management system.</p>
            </div>

            <div class="row g-4">
                <!-- Manage Customers Card -->
                <div class="col-md-4">
                    <div class="p-4 rounded-4 bg-white bg-opacity-75 border shadow-sm">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Customers</h5>
                                <span class="text-muted small">Manage user accounts</span>
                            </div>
                        </div>
                        <a href="manage_users.php" class="btn btn-outline-primary rounded-pill w-100 fw-semibold text-decoration-none">
                            View Customers <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Manage Policies Card -->
                <div class="col-md-4">
                    <div class="p-4 rounded-4 bg-white bg-opacity-75 border shadow-sm">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-file-shield"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Policies</h5>
                                <span class="text-muted small">Manage insurance packages</span>
                            </div>
                        </div>
                        <a href="manage_packages.php" class="btn btn-outline-success rounded-pill w-100 fw-semibold text-decoration-none">
                            View Policies <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>

                <!-- Manage Claims Card -->
                <div class="col-md-4">
                    <div class="p-4 rounded-4 bg-white bg-opacity-75 border shadow-sm">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center fs-4" style="width: 50px; height: 50px;">
                                <i class="fa-solid fa-file-invoice-dollar"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0">Claims</h5>
                                <span class="text-muted small">Review claim requests</span>
                            </div>
                        </div>
                        <a href="manage_claims.php" class="btn btn-warning text-white rounded-pill w-100 fw-semibold text-decoration-none">
                            Review Submissions <i class="fa-solid fa-arrow-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
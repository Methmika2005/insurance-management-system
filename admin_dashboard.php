<?php
session_start();
require_once 'db.php';

// Fetch summary stats
$total_customers = 0;
$result = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
if ($result) { $total_customers = $result->fetch_assoc()['count']; }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Insurance Portal</title>
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
            overflow-x: hidden;
        }
        .navbar-custom {
            background: linear-gradient(90deg, #0284c7 0%, #2563eb 100%);
            padding: 12px 0;
            backdrop-filter: blur(8px);
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1030;
        }
        /* Main layout wrapper to push content below fixed navbar */
        .main-wrapper {
            margin-top: 70px;
        }
        /* Fixed Sidebar Styling */
        .sidebar {
            background: rgba(15, 23, 42, 0.95);
            backdrop-filter: blur(12px);
            position: fixed;
            top: 70px;
            bottom: 0;
            left: 0;
            width: 260px;
            z-index: 1020;
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
            overflow-y: auto;
        }
        .sidebar .nav-link {
            color: #94a3b8;
            padding: 12px 20px;
            margin: 4px 12px;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            background: #2563eb;
            color: white;
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }
        .sidebar .nav-link i {
            margin-right: 12px;
            font-size: 18px;
        }
        /* Content area offset for fixed sidebar */
        .content-area {
            margin-left: 260px;
            padding: 30px;
        }
        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            .content-area {
                margin-left: 0;
                padding: 15px;
            }
        }
        /* Glass Components */
        .hero-banner {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.6);
        }
        .card-custom {
            border-radius: 20px;
            border: none;
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(12px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.06);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            background: rgba(255, 255, 255, 0.95);
        }
        .icon-box {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }
        .btn-custom-blue {
            background-color: #0284c7;
            color: white;
            border-radius: 50px;
            padding: 8px 20px;
            font-weight: 500;
            border: none;
        }
        .btn-custom-blue:hover {
            background-color: #0369a1;
            color: white;
        }
    </style>
</head>
<body>

    <!-- Top Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm">
        <div class="container-fluid px-4">
            <a class="navbar-brand fw-bold fs-4 d-flex align-items-center" href="#">
                <i class="fa-solid fa-shield-halved me-2"></i> Insurance Portal
            </a>
            <div class="d-flex align-items-center">
                <a href="#" class="text-white position-relative me-4 fs-5 text-decoration-none">
                    <i class="fa-regular fa-bell"></i>
                    <span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle"></span>
                </a>
                <div class="d-flex align-items-center text-white bg-white bg-opacity-10 px-3 py-1.5 rounded-pill border border-white border-opacity-25 me-3">
                    <i class="fa-solid fa-circle-user fs-4 me-2"></i>
                    <div class="text-start" style="line-height: 1.2;">
                        <small class="d-block text-white-50" style="font-size: 11px;">Welcome,</small>
                        <span class="fw-semibold" style="font-size: 13px;">Methmika Prabhashwara Bandara</span>
                    </div>
                </div>
                <a href="logout.php" class="btn btn-light btn-sm fw-semibold text-danger rounded-pill px-3 shadow-sm">
                    <i class="fa-solid fa-right-from-bracket me-1"></i> Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="main-wrapper">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link active" href="#"><i class="fa-solid fa-house"></i> Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa-solid fa-users"></i> Customers</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa-solid fa-file-contract"></i> Policies</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa-solid fa-file-circle-exclamation"></i> Claims</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa-solid fa-credit-card"></i> Payments</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa-solid fa-chart-bar"></i> Reports</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fa-solid fa-gear"></i> Settings</a>
                </li>
            </ul>
        </div>

        <!-- Main Content Area -->
        <div class="content-area">
            
            <!-- Welcome Hero Banner -->
            <div class="hero-banner p-4 p-md-4 mb-4 position-relative overflow-hidden">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <span class="text-uppercase fw-bold text-primary tracking-wider" style="font-size: 12px; letter-spacing: 1px;">WELCOME BACK</span>
                        <h1 class="display-6 fw-bold text-dark mt-1 mb-2">Admin Dashboard</h1>
                        <p class="text-muted mb-0 small" style="max-width: 600px;">
                            Manage system users, approve insurance policies, review claims, and monitor revenue transactions.
                        </p>
                    </div>
                    <div class="col-lg-4 text-end d-none d-lg-block">
                        <i class="fa-solid fa-shield-heart text-primary opacity-50" style="font-size: 80px;"></i>
                    </div>
                </div>
            </div>

            <!-- Quick Summary Stats Grid (4 Cards) -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card card-custom p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary">
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold">+12%</span>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark"><?php echo $total_customers; ?></h3>
                        <span class="text-muted small">Total Customers</span>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-custom p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="icon-box bg-success bg-opacity-10 text-success">
                                <i class="fa-solid fa-file-contract"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold">+8%</span>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark">12</h3>
                        <span class="text-muted small">Active Policies</span>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-custom p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="icon-box bg-warning bg-opacity-10 text-warning">
                                <i class="fa-solid fa-clock-history"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold">+5%</span>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark">3</h3>
                        <span class="text-muted small">Pending Claims</span>
                    </div>
                </div>

                <div class="col-xl-3 col-md-6">
                    <div class="card card-custom p-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <div class="icon-box bg-info bg-opacity-10 text-info">
                                <i class="fa-solid fa-sack-dollar"></i>
                            </div>
                            <span class="badge bg-success bg-opacity-10 text-success fw-bold">+18%</span>
                        </div>
                        <h3 class="fw-bold mb-0 text-dark">LKR 450K</h3>
                        <span class="text-muted small">Total Revenue</span>
                    </div>
                </div>
            </div>

            <!-- Middle Section: 3 Action Cards + Revenue Overview -->
            <div class="row g-4 mb-4">
                <!-- Action Cards Column -->
                <div class="col-xl-8">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="card card-custom h-100 p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="icon-box bg-primary bg-opacity-10 text-primary mb-3">
                                        <i class="fa-solid fa-users-gear"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1 fs-6">Manage Users</h5>
                                    <p class="text-muted small mb-3">View, edit, or manage registered customer accounts.</p>
                                </div>
                                <a href="#" class="btn btn-custom-blue w-100 btn-sm d-flex align-items-center justify-content-center">
                                    <span>View Users</span> <i class="fa-solid fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card card-custom h-100 p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="icon-box bg-success bg-opacity-10 text-success mb-3">
                                        <i class="fa-solid fa-folder-plus"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1 fs-6">Policy Packages</h5>
                                    <p class="text-muted small mb-3">Add new insurance plans or update existing rates.</p>
                                </div>
                                <a href="#" class="btn btn-success w-100 btn-sm d-flex align-items-center justify-content-center rounded-pill">
                                    <span>Manage</span> <i class="fa-solid fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card card-custom h-100 p-3 d-flex flex-column justify-content-between">
                                <div>
                                    <div class="icon-box bg-warning bg-opacity-10 text-warning mb-3">
                                        <i class="fa-solid fa-file-signature"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-1 fs-6">Review Claims</h5>
                                    <p class="text-muted small mb-3">Approve or reject insurance claim submissions.</p>
                                </div>
                                <a href="#" class="btn btn-warning text-white w-100 btn-sm d-flex align-items-center justify-content-center rounded-pill">
                                    <span>Review</span> <i class="fa-solid fa-arrow-right ms-2"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Revenue Overview Card -->
                <div class="col-xl-4">
                    <div class="card card-custom h-100 p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-dark mb-0">Revenue Overview</h6>
                            <span class="badge bg-light text-dark border">This Month <i class="fa-solid fa-chevron-down ms-1"></i></span>
                        </div>
                        <div class="text-center my-auto py-3">
                            <h3 class="fw-bold text-primary mb-1">LKR 450K</h3>
                            <p class="text-muted small mb-0">Total earnings tracked dynamically</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section: Recent Claims & Recent Users -->
            <div class="row g-4">
                <!-- Recent Claim Requests Table -->
                <div class="col-xl-8">
                    <div class="card card-custom p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0 fs-6"><i class="fa-solid fa-list-check me-2 text-primary"></i>Recent Claim Requests</h5>
                            <a href="#" class="text-primary small fw-bold text-decoration-none">View All</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Claim ID</th>
                                        <th>Customer</th>
                                        <th>Policy Type</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>#CLM-1001</td>
                                        <td>Methmika P.</td>
                                        <td>Vehicle</td>
                                        <td>LKR 75,000</td>
                                        <td><span class="badge bg-warning text-dark">Pending</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-success py-0 px-2 rounded-pill">Approve</button>
                                            <button class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill">Reject</button>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>#CLM-1002</td>
                                        <td>Nimal Perera</td>
                                        <td>Health</td>
                                        <td>LKR 120,000</td>
                                        <td><span class="badge bg-info">Under Review</span></td>
                                        <td>
                                            <button class="btn btn-sm btn-success py-0 px-2 rounded-pill">Approve</button>
                                            <button class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill">Reject</button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Users List -->
                <div class="col-xl-4">
                    <div class="card card-custom p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 fs-6">Recent Users</h6>
                            <a href="#" class="text-primary small fw-bold text-decoration-none">View All</a>
                        </div>
                        <div class="d-flex align-items-center mb-3 pb-2 border-bottom">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 me-3">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold small">Methmika Prabhashwara</h6>
                                <small class="text-muted" style="font-size: 11px;">methmika@example.com</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-2 me-3">
                                <i class="fa-solid fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-semibold small">Nimal Perera</h6>
                                <small class="text-muted" style="font-size: 11px;">nimal@example.com</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
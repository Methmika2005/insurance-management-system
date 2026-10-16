<?php
session_start();
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Approve / Reject
if (isset($_GET['action']) && isset($_GET['id'])) {
    $claim_id = intval($_GET['id']);
    $action   = $_GET['action'];

    if ($action === 'approve') {
        $status = 'Approved';
    } elseif ($action === 'reject') {
        $status = 'Rejected';
    }

    if (isset($status)) {
        $stmt = $conn->prepare("UPDATE claims SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $claim_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: manage_claims.php");
    exit();
}

// Fetch Claims with User & Package details
$claims = [];
$sql = "SELECT c.*, u.full_name, u.email, p.package_name 
        FROM claims c
        JOIN users u ON c.user_id = u.id
        JOIN packages p ON c.package_id = p.id
        ORDER BY c.id DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $claims[] = $row;
    }
}

$admin_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Claims - Insurance Portal</title>
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
        .table-custom thead th {
            background: #f8fafc;
            color: #64748b;
            font-weight: 600;
            padding: 14px;
            border: none;
        }
        .table-custom tbody td {
            padding: 14px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
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
            <li><a href="admin_dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
            <li><a href="manage_users.php"><i class="fa-solid fa-users"></i> Customers</a></li>
            <li><a href="manage_packages.php"><i class="fa-solid fa-file-shield"></i> Policies</a></li>
            <li><a href="manage_claims.php" class="active"><i class="fa-solid fa-file-invoice-dollar"></i> Claims</a></li>
            <li><a href="#"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
            <li><a href="#"><i class="fa-solid fa-chart-pie"></i> Reports</a></li>
            <li><a href="#"><i class="fa-solid fa-gear"></i> Settings</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Top Navbar -->
        <div class="top-navbar">
            <span class="text-muted small fw-semibold">Dashboard <i class="fa-solid fa-chevron-right mx-1"></i> Claims Management</span>
            <div class="d-flex align-items-center gap-3">
                <span class="fw-semibold small">Admin: <?php echo htmlspecialchars($admin_name); ?></span>
                <a href="admin_dashboard.php" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back
                </a>
            </div>
        </div>

        <!-- Glass Card Container -->
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Insurance Claim Requests</h4>
                    <p class="text-muted small mb-0">Review and manage claims submitted by customers.</p>
                </div>
            </div>

            <!-- Claims Table -->
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Claim ID</th>
                            <th>Customer Name</th>
                            <th>Policy Package</th>
                            <th>Claim Amount</th>
                            <th>Reason</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($claims)): ?>
                            <?php foreach ($claims as $row): ?>
                                <tr>
                                    <td><span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1">#CLM-<?php echo $row['id']; ?></span></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-dark"><?php echo htmlspecialchars($row['package_name']); ?></span></td>
                                    <td class="fw-bold text-dark">LKR <?php echo number_format($row['claim_amount'], 2); ?></td>
                                    <td class="text-muted small"><?php echo htmlspecialchars($row['reason']); ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'Approved'): ?>
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-1 rounded-pill">Approved</span>
                                        <?php elseif ($row['status'] === 'Rejected'): ?>
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1 rounded-pill">Rejected</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-1 rounded-pill">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($row['status'] === 'Pending'): ?>
                                            <a href="manage_claims.php?action=approve&id=<?php echo $row['id']; ?>" class="btn btn-success btn-sm rounded-pill px-3 me-1" onclick="return confirm('Approve this claim?');">
                                                <i class="fa-solid fa-check me-1"></i> Approve
                                            </a>
                                            <a href="manage_claims.php?action=reject&id=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm rounded-pill px-3" onclick="return confirm('Reject this claim?');">
                                                <i class="fa-solid fa-xmark me-1"></i> Reject
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted small">No action required</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">No claim submissions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
session_start();
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Add Customer/User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_user'])) {
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $password  = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role      = 'customer';

    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $full_name, $email, $password, $role);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php");
    exit();
}

// Handle Update Customer/User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_user'])) {
    $user_id   = intval($_POST['user_id']);
    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ? AND role = 'customer'");
    $stmt->bind_param("ssi", $full_name, $email, $user_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_users.php");
    exit();
}

// Handle Delete Customer
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    if ($delete_id !== $_SESSION['user_id']) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role = 'customer'");
        $stmt->bind_param("i", $delete_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: manage_users.php");
    exit();
}

// Fetch Customers into an array
$customers = [];
$sql = "SELECT id, full_name, email, created_at FROM users WHERE role = 'customer' ORDER BY id DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $customers[] = $row;
    }
}

$admin_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Customers - Insurance Portal</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
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
        .btn-action-view {
            background: #e0f2fe;
            color: #0284c7;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
        .btn-action-edit {
            background: #ccfbf1;
            color: #0d9488;
            border: none;
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
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
            <li><a href="manage_users.php" class="active"><i class="fa-solid fa-users"></i> Customers</a></li>
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
            <span class="text-muted small fw-semibold">Dashboard <i class="fa-solid fa-chevron-right mx-1"></i> Manage Customers</span>
            <div class="d-flex align-items-center gap-3">
                <span class="fw-semibold small">Admin: <?php echo htmlspecialchars($admin_name); ?></span>
                <a href="admin_dashboard.php" class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
                </a>
            </div>
        </div>

        <!-- Glass Card Container -->
        <div class="glass-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Registered Customers</h4>
                    <p class="text-muted small mb-0">View and manage registered customer accounts.</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="fa-solid fa-plus me-1"></i> Add New Customer
                </button>
            </div>

            <!-- Customers Table -->
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer Name</th>
                            <th>Email Address</th>
                            <th>Registered Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($customers)): ?>
                            <?php foreach ($customers as $row): ?>
                                <tr>
                                    <td><span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1">#<?php echo $row['id']; ?></span></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($row['full_name']); ?></td>
                                    <td class="text-muted"><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td class="text-muted small"><?php echo $row['created_at']; ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <!-- View Button -->
                                            <button class="btn-action-view" title="View Details" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $row['id']; ?>">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <!-- Edit Button -->
                                            <button class="btn-action-edit" title="Edit Customer" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <!-- Delete Button -->
                                            <a href="manage_users.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm rounded-3 px-3" onclick="return confirm('Are you sure you want to delete this customer?');">
                                                <i class="fa-solid fa-trash-can"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No registered customers found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modals rendered OUTSIDE the table and cards -->
    <?php foreach ($customers as $row): ?>
        <!-- View Modal -->
        <div class="modal fade" id="viewModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
              <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Customer Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-4">
                  <div class="text-center mb-4">
                      <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold fs-3 mb-2" style="width: 70px; height: 70px;">
                          <?php echo strtoupper(substr($row['full_name'], 0, 1)); ?>
                      </div>
                      <h4 class="fw-bold mb-0"><?php echo htmlspecialchars($row['full_name']); ?></h4>
                      <span class="badge bg-success bg-opacity-10 text-success mt-1">Customer Account</span>
                  </div>
                  <ul class="list-group list-group-flush rounded-3 overflow-hidden">
                      <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                          <span class="fw-semibold text-muted">User ID</span>
                          <span>#<?php echo $row['id']; ?></span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                          <span class="fw-semibold text-muted">Email Address</span>
                          <span class="fw-semibold"><?php echo htmlspecialchars($row['email']); ?></span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                          <span class="fw-semibold text-muted">Registered Date</span>
                          <span><?php echo $row['created_at']; ?></span>
                      </li>
                  </ul>
              </div>
              <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
              <form action="manage_users.php" method="POST">
                  <input type="hidden" name="user_id" value="<?php echo $row['id']; ?>">
                  <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold">Edit Customer Account</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body p-4">
                      <div class="mb-3">
                          <label class="form-label small fw-semibold">Full Name</label>
                          <input type="text" class="form-control rounded-3" name="full_name" value="<?php echo htmlspecialchars($row['full_name']); ?>" required>
                      </div>
                      <div class="mb-3">
                          <label class="form-label small fw-semibold">Email Address</label>
                          <input type="email" class="form-control rounded-3" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                      </div>
                  </div>
                  <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_user" class="btn btn-primary rounded-pill px-4 fw-semibold">Update Changes</button>
                  </div>
              </form>
            </div>
          </div>
        </div>
    <?php endforeach; ?>

    <!-- Add Customer Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
          <form action="manage_users.php" method="POST">
              <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Add New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-4">
                  <div class="mb-3">
                      <label class="form-label small fw-semibold">Full Name</label>
                      <input type="text" class="form-control rounded-3" name="full_name" required placeholder="Enter customer name">
                  </div>
                  <div class="mb-3">
                      <label class="form-label small fw-semibold">Email Address</label>
                      <input type="email" class="form-control rounded-3" name="email" required placeholder="Enter email address">
                  </div>
                  <div class="mb-3">
                      <label class="form-label small fw-semibold">Password</label>
                      <input type="password" class="form-control rounded-3" name="password" required placeholder="Enter password">
                  </div>
              </div>
              <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_user" class="btn btn-primary rounded-pill px-4 fw-semibold">Save Customer</button>
              </div>
          </form>
        </div>
      </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
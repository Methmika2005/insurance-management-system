<?php
session_start();
require_once 'db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Handle Add Package
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_package'])) {
    $package_name   = trim($_POST['package_name']);
    $category       = trim($_POST['category']);
    $description    = trim($_POST['description']);
    $premium_amount = floatval($_POST['premium_amount']);

    $stmt = $conn->prepare("INSERT INTO packages (package_name, category, description, premium_amount) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $package_name, $category, $description, $premium_amount);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_packages.php");
    exit();
}

// Handle Update Package
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_package'])) {
    $package_id     = intval($_POST['package_id']);
    $package_name   = trim($_POST['package_name']);
    $category       = trim($_POST['category']);
    $description    = trim($_POST['description']);
    $premium_amount = floatval($_POST['premium_amount']);

    $stmt = $conn->prepare("UPDATE packages SET package_name = ?, category = ?, description = ?, premium_amount = ? WHERE id = ?");
    $stmt->bind_param("sssdi", $package_name, $category, $description, $premium_amount, $package_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_packages.php");
    exit();
}

// Handle Delete Package
if (isset($_GET['delete'])) {
    $package_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM packages WHERE id = ?");
    $stmt->bind_param("i", $package_id);
    $stmt->execute();
    $stmt->close();
    header("Location: manage_packages.php");
    exit();
}

// Fetch packages into an array
$packages = [];
$sql = "SELECT * FROM packages ORDER BY id DESC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
}

$admin_name = isset($_SESSION['full_name']) ? $_SESSION['full_name'] : 'Admin';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Packages - Insurance Portal</title>
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
            <li><a href="manage_users.php"><i class="fa-solid fa-users"></i> Customers</a></li>
            <li><a href="manage_packages.php" class="active"><i class="fa-solid fa-file-shield"></i> Policies</a></li>
            <li><a href="#"><i class="fa-solid fa-file-invoice-dollar"></i> Claims</a></li>
            <li><a href="#"><i class="fa-solid fa-credit-card"></i> Payments</a></li>
            <li><a href="#"><i class="fa-solid fa-chart-pie"></i> Reports</a></li>
            <li><a href="#"><i class="fa-solid fa-gear"></i> Settings</a></li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        
        <!-- Top Navbar -->
        <div class="top-navbar">
            <span class="text-muted small fw-semibold">Dashboard <i class="fa-solid fa-chevron-right mx-1"></i> Manage Policy Packages</span>
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
                    <h4 class="fw-bold mb-1">Insurance Policy Packages</h4>
                    <p class="text-muted small mb-0">Create and manage available insurance plans.</p>
                </div>
                <button class="btn btn-primary rounded-pill px-4 fw-semibold shadow-sm" data-bs-toggle="modal" data-bs-target="#addPackageModal">
                    <i class="fa-solid fa-plus me-1"></i> Add New Package
                </button>
            </div>

            <!-- Packages Table -->
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Package Name</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Premium (LKR)</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($packages)): ?>
                            <?php foreach ($packages as $row): ?>
                                <tr>
                                    <td><span class="badge bg-primary bg-opacity-10 text-primary px-2 py-1">#<?php echo $row['id']; ?></span></td>
                                    <td class="fw-semibold"><?php echo htmlspecialchars($row['package_name']); ?></td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-dark"><?php echo htmlspecialchars($row['category']); ?></span></td>
                                    <td class="text-muted small"><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td class="fw-bold text-success">LKR <?php echo number_format($row['premium_amount'], 2); ?></td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center gap-2">
                                            <!-- View Button -->
                                            <button class="btn-action-view" title="View Details" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $row['id']; ?>">
                                                <i class="fa-solid fa-eye"></i>
                                            </button>
                                            <!-- Edit Button -->
                                            <button class="btn-action-edit" title="Edit Package" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">
                                                <i class="fa-solid fa-pen"></i>
                                            </button>
                                            <!-- Delete Button -->
                                            <a href="manage_packages.php?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm rounded-3 px-3" onclick="return confirm('Are you sure you want to delete this package?');">
                                                <i class="fa-solid fa-trash-can"></i> Delete
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No policy packages found. Click "Add New Package" to create one.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <!-- Modals rendered OUTSIDE the table and cards -->
    <?php foreach ($packages as $row): ?>
        <!-- View Modal -->
        <div class="modal fade" id="viewModal<?php echo $row['id']; ?>" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
              <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Package Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-4">
                  <div class="text-center mb-4">
                      <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center fw-bold fs-3 mb-2" style="width: 70px; height: 70px;">
                          <i class="fa-solid fa-shield-halved"></i>
                      </div>
                      <h4 class="fw-bold mb-0"><?php echo htmlspecialchars($row['package_name']); ?></h4>
                      <span class="badge bg-info text-dark mt-1">Category: <?php echo htmlspecialchars($row['category']); ?></span>
                  </div>
                  <ul class="list-group list-group-flush rounded-3 overflow-hidden">
                      <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                          <span class="fw-semibold text-muted">Package ID</span>
                          <span>#<?php echo $row['id']; ?></span>
                      </li>
                      <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                          <span class="fw-semibold text-muted">Premium Amount</span>
                          <span class="fw-bold text-success">LKR <?php echo number_format($row['premium_amount'], 2); ?></span>
                      </li>
                      <li class="list-group-item d-flex flex-column py-3">
                          <span class="fw-semibold text-muted mb-1">Description</span>
                          <span class="small text-dark"><?php echo htmlspecialchars($row['description']); ?></span>
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
              <form action="manage_packages.php" method="POST">
                  <input type="hidden" name="package_id" value="<?php echo $row['id']; ?>">
                  <div class="modal-header border-0 pb-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold">Edit Insurance Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body p-4">
                      <div class="mb-3">
                          <label class="form-label small fw-semibold">Package Name</label>
                          <input type="text" class="form-control rounded-3" name="package_name" value="<?php echo htmlspecialchars($row['package_name']); ?>" required>
                      </div>
                      <div class="mb-3">
                          <label class="form-label small fw-semibold">Category</label>
                          <select class="form-select rounded-3" name="category" required>
                              <option value="Health" <?php echo ($row['category'] == 'Health') ? 'selected' : ''; ?>>Health Insurance</option>
                              <option value="Motor" <?php echo ($row['category'] == 'Motor') ? 'selected' : ''; ?>>Motor Insurance</option>
                              <option value="Life" <?php echo ($row['category'] == 'Life') ? 'selected' : ''; ?>>Life Insurance</option>
                              <option value="Home" <?php echo ($row['category'] == 'Home') ? 'selected' : ''; ?>>Home Insurance</option>
                          </select>
                      </div>
                      <div class="mb-3">
                          <label class="form-label small fw-semibold">Description</label>
                          <textarea class="form-control rounded-3" name="description" rows="3" required><?php echo htmlspecialchars($row['description']); ?></textarea>
                      </div>
                      <div class="mb-3">
                          <label class="form-label small fw-semibold">Premium Amount (LKR)</label>
                          <input type="number" step="0.01" class="form-control rounded-3" name="premium_amount" value="<?php echo $row['premium_amount']; ?>" required>
                      </div>
                  </div>
                  <div class="modal-footer border-0 px-4 pb-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_package" class="btn btn-primary rounded-pill px-4 fw-semibold">Update Changes</button>
                  </div>
              </form>
            </div>
          </div>
        </div>
    <?php endforeach; ?>

    <!-- Add Package Modal -->
    <div class="modal fade" id="addPackageModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
          <form action="manage_packages.php" method="POST">
              <div class="modal-header border-0 pb-0 px-4 pt-4">
                <h5 class="modal-title fw-bold">Add New Insurance Package</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body p-4">
                  <div class="mb-3">
                      <label class="form-label small fw-semibold">Package Name</label>
                      <input type="text" class="form-control rounded-3" name="package_name" required placeholder="e.g. Gold Health Shield">
                  </div>
                  <div class="mb-3">
                      <label class="form-label small fw-semibold">Category</label>
                      <select class="form-select rounded-3" name="category" required>
                          <option value="Health">Health Insurance</option>
                          <option value="Motor">Motor Insurance</option>
                          <option value="Life">Life Insurance</option>
                          <option value="Home">Home Insurance</option>
                      </select>
                  </div>
                  <div class="mb-3">
                      <label class="form-label small fw-semibold">Description</label>
                      <textarea class="form-control rounded-3" name="description" rows="3" required placeholder="Enter package benefits..."></textarea>
                  </div>
                  <div class="mb-3">
                      <label class="form-label small fw-semibold">Premium Amount (LKR)</label>
                      <input type="number" step="0.01" class="form-control rounded-3" name="premium_amount" required placeholder="e.g. 15000.00">
                  </div>
              </div>
              <div class="modal-footer border-0 px-4 pb-4 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" name="add_package" class="btn btn-primary rounded-pill px-4 fw-semibold">Save Package</button>
              </div>
          </form>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
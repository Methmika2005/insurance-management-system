<?php
session_start();
require_once 'db.php';

// Check if customer is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'customer') {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT full_name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$amount = "5000.00"; 
$order_id = "ORD-" . uniqid();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Make Payment - Insurance Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        .card-custom {
            border-radius: 20px;
            border: none;
            background: rgba(255, 255, 255, 0.90);
            backdrop-filter: blur(12px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card card-custom p-4 p-md-5">
                    <div class="text-center mb-4">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-inline-flex p-3 mb-3">
                            <i class="fa-solid fa-credit-card fs-2"></i>
                        </div>
                        <h3 class="fw-bold">Pay Insurance Premium</h3>
                        <p class="text-muted small">Test Payment Simulation (Sandbox Mode)</p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Order ID</label>
                        <input type="text" class="form-control" value="<?php echo $order_id; ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Customer Name</label>
                        <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" readonly>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email Address</label>
                        <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Payment Amount (LKR)</label>
                        <input type="text" class="form-control fw-bold text-success fs-5" value="LKR <?php echo $amount; ?>" readonly>
                    </div>

                    <div class="d-grid gap-2">
                        <!-- Simulate Success Button -->
                        <a href="payhere_return.php" class="btn btn-success py-2 rounded-pill fw-semibold shadow-sm">
                            <i class="fa-solid fa-circle-check me-2"></i> Simulate Successful Payment
                        </a>
                        <!-- Simulate Cancel Button -->
                        <a href="payhere_cancel.php" class="btn btn-outline-danger py-2 rounded-pill fw-semibold shadow-sm">
                            <i class="fa-solid fa-circle-xmark me-2"></i> Simulate Cancel Payment
                        </a>
                    </div>
                    
                    <a href="customer_dashboard.php" class="btn btn-light w-100 py-2 rounded-pill fw-semibold mt-3 text-muted">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
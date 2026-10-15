<?php
session_start();
require_once 'db.php';

// Check if temporary login session exists
if (!isset($_SESSION['temp_user_id'])) {
    header("Location: login.php");
    exit();
}

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $entered_otp = trim($_POST['otp_code']);
    $user_id = $_SESSION['temp_user_id'];

    $stmt = $conn->prepare("SELECT otp_code, otp_expiry, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Check if OTP matches and hasn't expired
        if ($user['otp_code'] === $entered_otp) {
            if (strtotime($user['otp_expiry']) >= time()) {
                // Clear temporary sessions and set official login sessions
                unset($_SESSION['temp_user_id']);
                unset($_SESSION['temp_role']);
                unset($_SESSION['test_otp']); // Clear test otp

                $_SESSION['user_id'] = $user_id;
                $_SESSION['role'] = $user['role'];

                // Redirect based on user role
                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                } else {
                    header("Location: customer_dashboard.php");
                }
                exit();
            } else {
                $message = "OTP has expired. Please log in again.";
                $message_type = "danger";
            }
        } else {
            $message = "Invalid OTP code!";
            $message_type = "danger";
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Insurance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-success text-white text-center py-3">
                        <h4 class="mb-0">OTP Verification</h4>
                    </div>
                    <div class="card-body p-4">
                        
                        <!-- DEV MODE: Display OTP directly on screen -->
                        <?php if (isset($_SESSION['test_otp'])): ?>
                            <div class="alert alert-info text-center">
                                <strong>[DEV MODE] OTP Code: <span class="text-danger fs-3"><?php echo $_SESSION['test_otp']; ?></span></strong>
                            </div>
                        <?php endif; ?>

                        <?php if ($message != ""): ?>
                            <div class="alert alert-<?php echo $message_type; ?> text-center" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form action="verify_otp.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Enter 6-digit OTP Code</label>
                                <input type="text" name="otp_code" class="form-control text-center fs-4 tracking-wide" placeholder="------" maxlength="6" required>
                            </div>
                            <button type="submit" class="btn btn-success w-100 py-2">Verify & Login</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
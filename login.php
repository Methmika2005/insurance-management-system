<?php
session_start();
require_once 'db.php';

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, full_name, email, password_hash, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify Password
        if (password_verify($password, $user['password_hash'])) {
            // Generate 6-digit OTP
            $otp_code = rand(100000, 999999);
            $otp_expiry = date("Y-m-d H:i:s", strtotime("+10 minutes"));

            // Update user with OTP in database
            $update_stmt = $conn->prepare("UPDATE users SET otp_code = ?, otp_expiry = ? WHERE id = ?");
            $update_stmt->bind_param("ssi", $otp_code, $otp_expiry, $user['id']);
            $update_stmt->execute();
            $update_stmt->close();

            // Store OTP in session to display on screen directly (Testing Mode)
            $_SESSION['test_otp'] = $otp_code; 

            // Store temporary session for verification
            $_SESSION['temp_user_id'] = $user['id'];
            $_SESSION['temp_role'] = $user['role'];

            header("Location: verify_otp.php");
            exit();
        } else {
            $message = "Invalid Password!";
            $message_type = "danger";
        }
    } else {
        $message = "Email address not found!";
        $message_type = "danger";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Insurance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">Customer / Admin Login</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($message != ""): ?>
                            <div class="alert alert-<?php echo $message_type; ?> text-center" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form action="login.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Login & Generate OTP</button>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3 bg-white">
                        Don't have an account? <a href="register.php" class="text-primary fw-bold">Register Here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
require_once 'db.php';

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone_number = trim($_POST['phone_number']);
    $password = $_POST['password'];

    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $message = "This email is already registered!";
        $message_type = "danger";
    } else {
        // Secure password hashing
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);
        $role = 'customer';

        $stmt = $conn->prepare("INSERT INTO users (full_name, email, phone_number, password_hash, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $full_name, $email, $phone_number, $hashed_password, $role);

        if ($stmt->execute()) {
            $message = "Registration successful! You can now log in.";
            $message_type = "success";
        } else {
            $message = "Something went wrong. Please try again.";
            $message_type = "danger";
        }
        $stmt->close();
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Registration - Insurance System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg border-0 rounded-lg">
                    <div class="card-header bg-primary text-white text-center py-3">
                        <h4 class="mb-0">Customer Registration</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php if ($message != ""): ?>
                            <div class="alert alert-<?php echo $message_type; ?> text-center" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <form action="register.php" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" name="full_name" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email Address</label>
                                <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Phone Number</label>
                                <input type="text" name="phone_number" class="form-control" placeholder="0712345678" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 py-2">Register Account</button>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3 bg-white">
                        Already have an account? <a href="login.php" class="text-primary fw-bold">Login Here</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
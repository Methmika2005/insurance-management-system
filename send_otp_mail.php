<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

function sendOTP_Email($recipient_email, $recipient_name, $otp_code) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'prabhashwaramethmika@gmail.com';        
        $mail->Password   = 'Methmika200509';     
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('prabhashwaramethmika@gmail.com', 'Insurance Management System');
        $mail->addAddress($recipient_email, $recipient_name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Your Login Verification OTP Code';
        $mail->Body    = "
            <div style='font-family: Arial, sans-serif; padding: 20px; border: 1px solid #ddd; border-radius: 5px;'>
                <h2 style='color: #0d6efd;'>Insurance Management System</h2>
                <p>Hello <b>{$recipient_name}</b>,</p>
                <p>Your One-Time Password (OTP) for secure login is:</p>
                <h1 style='background: #f8f9fa; padding: 10px; text-align: center; letter-spacing: 5px; color: #333;'>{$otp_code}</h1>
                <p>This code is valid for 10 minutes. Do not share this code with anyone.</p>
                <br>
                <p>Best regards,</p>
                <p><b>Insurance Team</b></p>
            </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'src/PHPMailer.php';
require 'src/SMTP.php';
require 'src/Exception.php';

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'coderay231@gmail.com'; // Your Gmail
    $mail->Password   = 'zebm wluz tedz qhnt'; // Use App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Email Content
    $mail->setFrom('coderay231@gmail.com', 'BloodConnect');
    $mail->addAddress('rayedriasat@gmail.com', 'Rayed R'); // Change recipient
    $mail->Subject = 'Test Email from PHPMailer';
    $mail->Body    = 'This is a test email 2 from PHPMailer without Composer.';

    $mail->send();
    echo 'Email sent successfully!';
} catch (Exception $e) {
    echo "Email could not be sent. Error: {$mail->ErrorInfo}";
}

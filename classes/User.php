<?php
require_once __DIR__ . '/../src/PHPMailer.php';
require_once __DIR__ . '/../src/SMTP.php';
require_once __DIR__ . '/../src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class User
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function register($name, $email, $phone, $password)
    {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO Users (name, email, phone_number, password) 
                VALUES (:name, :email, :phone, :password)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':password' => $hashed_password
            ]);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function login($email, $password)
    {
        $sql = "SELECT * FROM Users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Check if 2FA is enabled
            if ($user['two_factor_enabled']) {
                return [
                    'requires_2fa' => true,
                    'user_id' => $user['user_id'],
                    'email' => $user['email']
                ];
            }
            return $user;
        }
        return false;
    }

    public function generateVerificationCode($userId)
    {
        $code = sprintf("%06d", random_int(0, 999999));

        // Delete any existing unused codes for this user
        $sql = "DELETE FROM VerificationCodes WHERE user_id = :user_id AND is_used = FALSE";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':user_id' => $userId]);

        // Insert new code
        $sql = "INSERT INTO VerificationCodes (user_id, code) VALUES (:user_id, :code)";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':code' => $code
            ]);
            return $code;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function verifyCode($userId, $code)
    {
        $sql = "SELECT * FROM VerificationCodes 
                WHERE user_id = :user_id 
                AND code = :code 
                AND is_used = FALSE 
                AND expires_at > NOW()
                ORDER BY created_at DESC 
                LIMIT 1";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $userId,
                ':code' => $code
            ]);

            if ($stmt->fetch()) {
                // Mark code as used
                $updateSql = "UPDATE VerificationCodes 
                             SET is_used = TRUE 
                             WHERE user_id = :user_id 
                             AND code = :code";
                $stmt = $this->db->prepare($updateSql);
                $stmt->execute([
                    ':user_id' => $userId,
                    ':code' => $code
                ]);
                return true;
            }
            return false;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function createPasswordReset($email)
    {
        $sql = "SELECT user_id FROM Users WHERE email = :email";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        $token = bin2hex(random_bytes(32));
        $sql = "INSERT INTO PasswordResets (user_id, token) VALUES (:user_id, :token)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':user_id' => $user['user_id'],
                ':token' => $token
            ]);
            return $token;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function verifyPasswordReset($token)
    {
        $sql = "SELECT user_id FROM PasswordResets 
                WHERE token = :token 
                AND is_used = FALSE 
                AND expires_at > NOW()";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':token' => $token]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function resetPassword($token, $newPassword)
    {
        $userData = $this->verifyPasswordReset($token);
        if (!$userData) {
            return false;
        }

        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);

        try {
            $this->db->beginTransaction();

            // Update password
            $sql = "UPDATE Users SET password = :password WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':password' => $hashed_password,
                ':user_id' => $userData['user_id']
            ]);

            // Mark token as used
            $sql = "UPDATE PasswordResets SET is_used = TRUE WHERE token = :token";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':token' => $token]);

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            return false;
        }
    }

    public function sendVerificationEmail($email, $code)
    {
        require_once __DIR__ . '/../src/PHPMailer.php';
        require_once __DIR__ . '/../src/SMTP.php';
        require_once __DIR__ . '/../src/Exception.php';

        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'coderay231@gmail.com'; // Your Gmail
            $mail->Password   = 'zebm wluz tedz qhnt'; // Your App Password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Email Content
            $mail->setFrom('coderay231@gmail.com', 'BloodConnect');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your BloodConnect Verification Code';

            $mail->Body = "
            <html>
            <head>
                <style>
                    .container {
                        padding: 20px;
                        font-family: Arial, sans-serif;
                    }
                    .code {
                        font-size: 24px;
                        font-weight: bold;
                        color: #2563eb;
                        padding: 10px;
                        background-color: #f3f4f6;
                        border-radius: 5px;
                        margin: 10px 0;
                    }
                    .warning {
                        color: #dc2626;
                        font-size: 14px;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Your Verification Code</h2>
                    <p>Please use the following code to complete your login:</p>
                    <div class='code'>$code</div>
                    <p>This code will expire in 10 minutes.</p>
                    <p class='warning'>If you didn't request this code, please ignore this email and ensure your account security.</p>
                </div>
            </body>
            </html>";

            $mail->AltBody = "Your verification code is: $code\nThis code will expire in 10 minutes.";

            return $mail->send();
        } catch (Exception $e) {
            // Log the error (implement proper logging in production)
            error_log("Email sending failed: " . $mail->ErrorInfo);
            return false;
        }
    }

    public function sendPasswordResetEmail($email, $token)
    {
        $mail = new PHPMailer(true);

        try {
            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'coderay231@gmail.com';
            $mail->Password   = 'zebm wluz tedz qhnt';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;

            // Email Content
            $mail->setFrom('coderay231@gmail.com', 'BloodConnect');
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your BloodConnect Password';

            $reset_link = 'http://localhost/bconnect/views/auth/reset-password.php?token=' . $token;

            $mail->Body = "
            <html>
            <head>
                <style>
                    .container {
                        padding: 20px;
                        font-family: Arial, sans-serif;
                    }
                    .button {
                        background-color: #2563eb;
                        color: white;
                        padding: 10px 20px;
                        text-decoration: none;
                        border-radius: 5px;
                        display: inline-block;
                        margin: 20px 0;
                    }
                    .warning {
                        color: #dc2626;
                        font-size: 14px;
                        margin-top: 20px;
                    }
                </style>
            </head>
            <body>
                <div class='container'>
                    <h2>Password Reset Request</h2>
                    <p>We received a request to reset your password. Click the button below to reset it:</p>
                    <a href='$reset_link' class='button'>Reset Password</a>
                    <p>This link will expire in 1 hour.</p>
                    <p class='warning'>If you didn't request this reset, please ignore this email and ensure your account security.</p>
                </div>
            </body>
            </html>";

            $mail->AltBody = "Reset your password by clicking this link: $reset_link\nThis link will expire in 1 hour.";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Password reset email sending failed: " . $mail->ErrorInfo);
            return false;
        }
    }

    // Add these methods to reduce duplicate queries
    public function emailExists($email) {
        $stmt = $this->db->prepare("SELECT user_id FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }
    
    public function phoneExists($phone) {
        $stmt = $this->db->prepare("SELECT user_id FROM Users WHERE phone_number = ?");
        $stmt->execute([$phone]);
        return $stmt->rowCount() > 0;
    }
    
    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

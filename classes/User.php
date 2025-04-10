<?php
require_once __DIR__ . '/../phpmailer_src/PHPMailer.php';
require_once __DIR__ . '/../phpmailer_src/SMTP.php';
require_once __DIR__ . '/../phpmailer_src/Exception.php';

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

        $sql = "INSERT INTO Users (name, email, phone_number, password_hash) 
                VALUES (:name, :email, :phone, :password_hash)";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':phone' => $phone,
                ':password_hash' => $hashed_password
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

        if ($user && password_verify($password, $user['password_hash'])) {
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

    public function verifyPasswordReset($userId, $token)
    {
        $sql = "SELECT * FROM PasswordResets 
                WHERE user_id = :user_id 
                AND token = :token 
                AND is_used = FALSE 
                AND expires_at > NOW()";

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':user_id' => $userId, ':token' => $token]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return false;
        }
    }

    public function resetPassword($userId, $token, $newPassword)
    {
        $userData = $this->verifyPasswordReset($userId, $token);
        if (!$userData) {
            return false;
        }

        $hashed_password = password_hash($newPassword, PASSWORD_DEFAULT);

        try {
            $this->db->beginTransaction();

            // Update password
            $sql = "UPDATE Users SET password_hash = :password_hash WHERE user_id = :user_id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':password_hash' => $hashed_password,
                ':user_id' => $userId
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
        $mail = new PHPMailer(true);

        try {
            // Load mail configuration
            require_once __DIR__ . '/../config/mail_config.php';

            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_ENCRYPTION;
            $mail->Port       = MAIL_PORT;

            // Email Content
            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Your BloodConnect Verification Code';

            // Rest of the email content remains the same
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
            // Load mail configuration
            require_once __DIR__ . '/../config/mail_config.php';

            // SMTP Configuration
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_ENCRYPTION;
            $mail->Port       = MAIL_PORT;

            // Email Content
            $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = 'Reset Your BloodConnect Password';
            $user_id = $this->getByEmail($email);
            // Detect environment based on server name
            $environment = (strpos($_SERVER['SERVER_NAME'] ?? '', 'infinityfree') !== false || strpos($_SERVER['SERVER_NAME'] ?? '', '42web.io') !== false) ? 'production' : 'development';
            if ($environment === 'production') {
                define('BASE_URL', 'http://coderay.42web.io/bconnect');
            } else {
                define('BASE_URL', 'http://localhost/bconnect');
            }
            
            $reset_link = BASE_URL . '/views/auth/reset-password.php?token=' . urlencode($token) . '&user_id=' . urlencode($user_id['user_id']);

            // Rest of the email content remains the same
            $mail->Body = "
            <html>
            <head>
                <style>
                    .container {
                        padding: 20px;
                        font-family: Arial, sans-serif;
                    }
                    .button {
                        background-color: #1d4ed8;  /* Darker blue for better contrast */
                        color: white !important;    /* Force white text */
                        padding: 12px 24px;         /* Larger padding */
                        font-size: 16px;            /* Larger font size */
                        text-decoration: none;
                        border-radius: 6px;         /* Slightly rounded corners */
                        display: inline-block;
                        margin: 20px 0;
                        border: 2px solid #1e40af;  /* Add border */
                        transition: all 0.3s ease;  /* Smooth hover effect */
                        box-shadow: 0 2px 4px rgba(0,0,0,0.1); /* Add subtle shadow */
                    }
                    .button:hover {
                        background-color: #1e40af;  /* Darker hover state */
                        transform: translateY(-1px); /* Lift effect on hover */
                        box-shadow: 0 4px 6px rgba(0,0,0,0.15); /* Enhanced shadow on hover */
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
                    <a href='$reset_link' class='button'>Reset Password Now →</a>
                    <p>This link will expire in 10 minutes.</p>
                    <p class='warning'>If you didn't request this reset, please ignore this email and ensure your account security.</p>
                </div>
            </body>
            </html>";

            $mail->AltBody = "Reset your password by clicking this link: $reset_link\nThis link will expire in 10 minutes.";

            return $mail->send();
        } catch (Exception $e) {
            error_log("Password reset email sending failed: " . $mail->ErrorInfo);
            return false;
        }
    }

    // Add these methods to reduce duplicate queries
    public function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT user_id FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->rowCount() > 0;
    }

    public function phoneExists($phone)
    {
        $stmt = $this->db->prepare("SELECT user_id FROM Users WHERE phone_number = ?");
        $stmt->execute([$phone]);
        return $stmt->rowCount() > 0;
    }

    public function getByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM Users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

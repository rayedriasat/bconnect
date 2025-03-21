<?php

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
            return $user;
        }
        return false;
    }

    public function generateVerificationCode($userId)
    {
        $code = sprintf("%06d", random_int(0, 999999));

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
}

<?php

namespace App\Models;

use PDO;
use PDOException;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class UserModel extends Database
{
    private $pdo;

    public function __construct()
    {
        $conn = $this->getConnection();
        $this->pdo = $conn;
    }

    public function getByEmail($email)
    {
        try {
            $stm = $this->pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stm->execute([$email]);

            if ($stm->rowCount() > 0) {
                return $stm->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch (PDOException $e) {
            throw $e;
        }
    }

    public function generateToken($user)
    {
        $payload = [
            'id' => $user['id'],
            'email' => $user['email'],
            'role' => $user['role'],
            'exp' => time() + (60 * 60 * 24) // 24 horas
        ];

        return JWT::encode($payload, JWT_SECRET, 'HS256');
    }
}
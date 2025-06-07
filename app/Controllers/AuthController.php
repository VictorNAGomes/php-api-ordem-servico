<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Http\Response;
use App\Http\Request;
use PDOException;

class AuthController 
{
    public function login()
    {
        $data = Request::body();

        if (!isset($data['email']) || !isset($data['password'])) {
            Response::json([
                'error' => true,
                'message' => 'Email and password are required'
            ], 400);
            return;
        }

        $userModel = new UserModel();
        
        try {
            $user = $userModel->getByEmail($data['email']);
            
            if (!$user || !password_verify($data['password'], $user['password'])) {
                Response::json([
                    'error' => true,
                    'message' => 'Invalid credentials'
                ], 401);
                return;
            }

            $token = $userModel->generateToken($user);
            
            Response::json([
                'error' => false,
                'data' => [
                    'token' => $token,
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name'],
                        'email' => $user['email'],
                        'role' => $user['role']
                    ]
                ]
            ]);
        } catch (PDOException $e) {
            Response::json([
                'error' => true,
                'message' => 'Error during login'
            ], 500);
        }
    }
}
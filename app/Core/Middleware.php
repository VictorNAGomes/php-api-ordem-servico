<?php

namespace App\Core;

use App\Models\UserModel;
use App\Http\Response;

class Middleware
{
    public static function auth()
    {
        $userModel = new UserModel();
        if (!$userModel->getCurrentUser()) {
            Response::json([
                'error' => true,
                'message' => 'Unauthorized'
            ], 401);
            exit;
        }
    }

    public static function admin()
    {
        $userModel = new UserModel();
        $user = $userModel->getCurrentUser();
        
        if (!$user || $user['role'] !== 'admin') {
            Response::json([
                'error' => true,
                'message' => 'Access denied. Admin role required.'
            ], 403);
            exit;
        }
    }
}
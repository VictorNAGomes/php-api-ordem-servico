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
} 
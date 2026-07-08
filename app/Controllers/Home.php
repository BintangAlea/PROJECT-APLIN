<?php

namespace App\Controllers;

use App\Models\UsersModel;

class Home
{
    public function index()
    {
        $isLoggedIn = isset($_SESSION['user_id']);
        $salonHistory = [];
        $cafeHistory = [];

        if ($isLoggedIn) {
            $userModel = new UsersModel();
            $salonHistory = $userModel->getSalonHistory((int)$_SESSION['user_id']);
            $cafeHistory = $userModel->getCafeHistory($_SESSION['full_name'] ?? '');
        }

        require __DIR__ . '/../Views/Home/home.php';
    }
}

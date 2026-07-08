<?php

namespace App\Controllers;

class ServicesController
{
    /**
     * Display services page
     */
    public function index()
    {
        $isLoggedIn = isset($_SESSION['user_id']);
        $salonHistory = [];
        $cafeHistory = [];

        if ($isLoggedIn) {
            $userModel = new \App\Models\UsersModel();
            $salonHistory = $userModel->getSalonHistory((int)$_SESSION['user_id']);
            $cafeHistory = $userModel->getCafeHistory($_SESSION['full_name'] ?? '');
        }

        return [
            'view' => 'Services.index',
            'data' => [
                'salonHistory' => $salonHistory,
                'cafeHistory' => $cafeHistory
            ]
        ];
    }
}

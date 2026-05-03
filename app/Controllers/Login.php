<?php

namespace App\Controllers;

class Login
{
    public function index(): void
    {
        require_once __DIR__ . '/../Views/Login/index.php';
    }
}

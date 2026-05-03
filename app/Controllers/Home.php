<?php

namespace App\Controllers;

class Home
{
    public function index(): void
    {
        require_once __DIR__ . '/../Views/Home/index.php';
    }
}

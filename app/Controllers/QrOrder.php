<?php

namespace App\Controllers;

class QrOrder
{
    public function index(): void
    {
        require_once __DIR__ . '/../Views/QrOrder/index.php';
    }
}

<?php

namespace App\Controllers;

class Home
{
    public function index()
    {
        require __DIR__ . '/../Views/Home/home.php';
    }
}

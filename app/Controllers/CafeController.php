<?php

namespace App\Controllers;

class CafeController
{
    /**
     * Display cafe menu page
     */
    public function index()
    {
        return [
            'view' => 'Cafe.index',
            'data' => []
        ];
    }
}

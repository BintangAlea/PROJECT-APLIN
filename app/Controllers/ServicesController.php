<?php

namespace App\Controllers;

class ServicesController
{
    /**
     * Display services page
     */
    public function index()
    {
        return [
            'view' => 'Services.index',
            'data' => []
        ];
    }
}

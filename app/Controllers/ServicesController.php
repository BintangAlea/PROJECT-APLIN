<?php

namespace App\Controllers;

use App\Models\ServicesModel;
use App\Models\BeauticiansModel;

class ServicesController
{
    private ServicesModel $servicesModel;
    private BeauticiansModel $beauticiansModel;

    public function __construct()
    {
        $this->servicesModel = new ServicesModel();
        $this->beauticiansModel = new BeauticiansModel();
    }

    public function index()
    {
        $category = $_GET['category'] ?? 'ALL';
        $services = $this->servicesModel->findAll();
        
        if ($category !== 'ALL') {
            $services = array_filter($services, fn($s) => $s['category'] === $category);
        }
        
        $beauticians = $this->beauticiansModel->findAll();
        $categories = ['Hair', 'Nails', 'Lashes', 'Wax & Eyebrows'];
        
        require __DIR__ . '/../Views/Services/index.php';
    }

    public function detail($serviceId)
    {
        $service = $this->servicesModel->findById($serviceId);
        if (!$service) {
            $_SESSION['error'] = 'Layanan tidak ditemukan';
            header('Location: index.php?page=services');
            exit;
        }
        require __DIR__ . '/../Views/Services/detail.php';
    }
}

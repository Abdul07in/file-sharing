<?php

namespace App\Controller;

class HomeController
{
    public function index()
    {
        $view = 'upload';
        require __DIR__ . '/../../views/layout.php';
    }

    public function receive()
    {
        $view = 'download';
        require __DIR__ . '/../../views/layout.php';
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // Página inicial do admin (resumos, atalhos para relatórios e logs)
        return view('admin.dashboard');
    }
}

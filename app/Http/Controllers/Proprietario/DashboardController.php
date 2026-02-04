<?php

namespace App\Http\Controllers\Proprietario;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('proprietario.dashboard');
    }
}

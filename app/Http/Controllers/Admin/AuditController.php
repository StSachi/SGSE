<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Audit;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $audits = Audit::with('user')->orderBy('created_at','desc')->paginate(50);
        return view('audits.index', compact('audits'));
    }
}

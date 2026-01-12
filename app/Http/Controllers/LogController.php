<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Log;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logs = Log::with('user')->orderBy('created_at', 'desc')->get();

        return Inertia::render('Log/Index', compact('logs'));   
    }
}

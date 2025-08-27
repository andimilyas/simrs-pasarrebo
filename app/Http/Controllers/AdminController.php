<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index()
    {
        return view('admin.dashboard');
    }

    /**
     * Display the admin users management.
     */
    public function users()
    {
        return view('admin.users');
    }

    /**
     * Display the admin settings.
     */
    public function settings()
    {
        return view('admin.settings');
    }
}

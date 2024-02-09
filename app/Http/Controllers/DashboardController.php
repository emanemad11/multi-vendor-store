<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {

        $title = 'Store';

        $user = Auth::user();

        // Return response: view, josn, redirect, file

        return view('dashboard.index', [
            'user' => 'Mohammed',
            'title' => $title
        ]);
    }
}

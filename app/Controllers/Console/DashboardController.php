<?php

namespace App\Controllers\Console;

use App\Controllers\Controller;

class DashboardController extends Controller
{

    public function index()
    {
        render('dashboard', [], 'default', 'console');
    }

}
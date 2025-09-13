<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'total_members' => 1234,
                'active_loans' => 2400000,
                'total_savings' => 15200000,
                'monthly_transactions' => 3800000,
            ],
        ]);
    }
}




<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard.index', [
            'stats' => [
                [
                    'label' => 'Your role',
                    'value' => auth()->user()->role->label(),
                ],
                [
                    'label' => 'Account',
                    'value' => auth()->user()->email,
                ],
                [
                    'label' => 'Status',
                    'value' => 'Active',
                ],
            ],
        ]);
    }
}

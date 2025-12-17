<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Invoice;
use App\Models\User;
use App\Models\ActivityLog;

class DashboardController extends Controller
{
    public function index()
    {
        $role = auth()->user()->role;

        $data = [
            'activeProjectsCount' => Project::where('active', true)->count(),
            'totalMembersCount' => User::count(),
            'recentLogs' => ActivityLog::with('user')->latest()->take(5)->get(),
            'activeProjects' => Project::where('active', true)->with('client')->take(5)->get(), // Mini list
        ];

        if ($role === 'CEO') {
            $data['totalRevenue'] = Invoice::where('status', 'Paid')->sum('amount');
            $data['pendingInvoicesCount'] = Invoice::where('status', 'Pending')->count();
        }

        return view('dashboard', $data);
    }
}

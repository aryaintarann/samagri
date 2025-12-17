<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Project;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return redirect()->back();
        }

        // Search Projects
        $projects = Project::where('name', 'like', "%{$query}%")
            ->orWhere('status', 'like', "%{$query}%")
            ->with('client')
            ->get();

        // Search Clients
        $clients = Client::where('name', 'like', "%{$query}%")
            ->orWhere('company', 'like', "%{$query}%")
            ->orWhere('email', 'like', "%{$query}%")
            ->get();

        // Search Invoices
        $invoices = Invoice::where('invoice_number', 'like', "%{$query}%")
            ->with(['project', 'project.client'])
            ->get();

        return view('search.results', compact('projects', 'clients', 'invoices', 'query'));
    }
}

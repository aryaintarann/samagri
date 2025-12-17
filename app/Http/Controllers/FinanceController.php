<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Expense;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FinanceController extends Controller
{
    public function index()
    {
        // CEO Only Middleware checks this, but redundant check ok
        if (auth()->user()->role !== 'CEO') {
            abort(403);
        }

        $totalRevenue = Invoice::where('status', 'Paid')->sum('amount');
        $totalExpenses = Expense::sum('amount');
        $profit = $totalRevenue - $totalExpenses;

        $expenses = Expense::latest()->get();
        // Paid invoices for context
        $invoices = Invoice::where('status', 'Paid')->latest()->take(10)->get();

        return view('finance.index', compact('totalRevenue', 'totalExpenses', 'profit', 'expenses', 'invoices'));
    }

    public function storeExpense(Request $request)
    {
        if (auth()->user()->role !== 'CEO') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        $expense = Expense::create($validated);

        if ($request->ajax()) {
            return response()->json(['message' => 'Expense added successfully', 'expense' => $expense]);
        }

        return redirect()->route('finance.index')->with('success', 'Expense added successfully.');
    }

    public function destroyExpense(Expense $expense)
    {
        if (auth()->user()->role !== 'CEO') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $expense->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Expense deleted successfully']);
        }

        return redirect()->route('finance.index')->with('success', 'Expense deleted successfully.');
    }
}

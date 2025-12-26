<?php

namespace App\Http\Controllers;

use App\Http\Requests\Clients\StoreClientRequest;
use App\Http\Requests\Clients\UpdateClientRequest;
use App\Models\Client;
use App\Traits\LogsActivity;

class ClientController extends Controller
{
    use LogsActivity;

    public function index()
    {
        $clients = Client::latest()->get();
        return view('clients.index', compact('clients'));
    }

    public function create()
    {
        return redirect()->route('clients.index');
    }

    public function store(StoreClientRequest $request)
    {
        $client = Client::create($request->validated());
        $this->logActivity('Created Client', 'Added new client: ' . $client->name);

        if ($request->ajax()) {
            return response()->json(['message' => 'Client created successfully', 'client' => $client]);
        }

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        if (request()->ajax()) {
            return response()->json($client);
        }
        return view('clients.show', compact('client'));
    }

    public function edit(Client $client)
    {
        if (request()->ajax()) {
            return response()->json($client);
        }
        return view('clients.edit', compact('client'));
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $client->update($request->validated());

        if ($request->ajax()) {
            return response()->json(['message' => 'Client updated successfully', 'client' => $client]);
        }

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $client->delete();

        if (request()->ajax()) {
            return response()->json(['message' => 'Client deleted successfully']);
        }

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }
}

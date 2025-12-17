<x-app-layout>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Search Results for "{{ $query }}"</h1>

        @if($projects->isEmpty() && $clients->isEmpty() && $invoices->isEmpty())
            <div class="bg-white rounded-2xl p-10 text-center shadow-sm">
                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-search text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No results found</h3>
                <p class="mt-1 text-gray-500">Try adjusting your search query.</p>
            </div>
        @else
            <div class="space-y-12">
                <!-- Projects -->
                @if($projects->isNotEmpty())
                    <section>
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-project-diagram mr-2 text-indigo-600"></i> Projects
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($projects as $project)
                                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                    <h3 class="font-bold text-lg text-gray-900">{{ $project->name }}</h3>
                                    <p class="text-sm text-gray-500 mb-2">{{ $project->client->name }}</p>
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold uppercase tracking-wider
                                                    {{ $project->status === 'Completed' ? 'bg-green-100 text-green-800' : ($project->status === 'In Progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $project->status }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- Clients -->
                @if($clients->isNotEmpty())
                    <section>
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-users mr-2 text-blue-600"></i> Clients
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($clients as $client)
                                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                    <h3 class="font-bold text-lg text-gray-900">{{ $client->name }}</h3>
                                    <p class="text-sm text-gray-500">{{ $client->company }}</p>
                                    <p class="text-xs text-gray-400 mt-2">{{ $client->email }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif

                <!-- Invoices -->
                @if($invoices->isNotEmpty())
                    <section>
                        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-file-invoice-dollar mr-2 text-green-600"></i> Invoices
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($invoices as $invoice)
                                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                                    <div class="flex justify-between mb-2">
                                        <h3 class="font-bold text-gray-900">{{ $invoice->invoice_number }}</h3>
                                        <span
                                            class="text-xs font-bold px-2 py-1 rounded {{ $invoice->status === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $invoice->status }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-500">{{ $invoice->project->name }}</p>
                                    <p class="text-lg font-bold text-gray-900 mt-2">Rp
                                        {{ number_format($invoice->amount, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        @endif
    </div>
</x-app-layout>
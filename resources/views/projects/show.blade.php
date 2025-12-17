<x-app-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800  leading-tight">
                Project Details: <span class="text-blue-500">{{ $project->name }}</span>
            </h2>
            <a href="{{ route('projects.index') }}"
                class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow transition transform hover:scale-105">
                <i class="fas fa-arrow-left mr-2"></i> Back
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Project Info -->
            <div class="col-span-2 bg-white  overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 border-b border-gray-100  font-bold text-lg text-gray-800 ">
                    Project Information
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="block text-sm text-gray-500 ">Client</span>
                            <span class="block text-lg font-semibold text-gray-800 ">{{ $project->client->name }}</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500 ">Status</span>
                            <span
                                class="inline-block px-3 py-1 mt-1 font-semibold text-sm rounded-full 
                                {{ $project->status === 'Completed' ? 'bg-green-100 text-green-800' : ($project->status === 'In Progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $project->status }}
                            </span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500 ">Budget</span>
                            <span class="block text-lg font-semibold text-gray-800 ">Rp
                                {{ number_format($project->budget, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="block text-sm text-gray-500 ">Deadline</span>
                            <span
                                class="block text-lg font-semibold text-gray-800 ">{{ $project->deadline ? $project->deadline->format('M d, Y') : 'N/A' }}</span>
                        </div>
                    </div>
                    <div>
                        <span class="block text-sm text-gray-500 ">Description</span>
                        <p class="mt-1 text-gray-800 ">
                            {{ $project->description ?: 'No description provided.' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Invoices? Tasks? -->
            <div class="bg-white  overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 border-b border-gray-100  font-bold text-lg text-gray-800 ">
                    Related Invoices
                </div>
                <div class="p-6">
                    @if($project->invoices->count() > 0)
                        <ul class="space-y-3">
                            @foreach($project->invoices as $invoice)
                                <li class="flex justify-between items-center bg-gray-50  p-3 rounded">
                                    <div>
                                        <div class="text-sm font-bold text-gray-800 ">Inv #{{ $invoice->id }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '' }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm font-bold text-gray-800 ">
                                            Rp {{ number_format($invoice->amount, 0, ',', '.') }}</div>
                                        <div
                                            class="text-xs {{ $invoice->status == 'Paid' ? 'text-green-500' : 'text-red-500' }}">
                                            {{ $invoice->status }}
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-gray-500  text-sm">No invoices found for this project.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
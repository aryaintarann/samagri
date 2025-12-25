<x-app-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800  leading-tight">
                Project Details: <span class="text-blue-500">{{ $project->name }}</span>
            </h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('projects.kanban', $project) }}"
                    class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded shadow transition transform hover:scale-105">
                    <i class="fas fa-columns mr-2"></i> Kanban Board
                </a>
                <a href="{{ route('projects.index') }}"
                    class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded shadow transition transform hover:scale-105">
                    <i class="fas fa-arrow-left mr-2"></i> Back
                </a>
            </div>
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
                                {{ $project->status === \App\Enums\ProjectStatus::COMPLETED ? 'bg-green-100 text-green-800' : ($project->status === \App\Enums\ProjectStatus::IN_PROGRESS ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $project->status->label() }}
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

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Invoices -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b border-gray-100 font-bold text-lg text-gray-800">
                        Related Invoices
                    </div>
                    <div class="p-6">
                        @if($project->invoices->count() > 0)
                            <ul class="space-y-3">
                                @foreach($project->invoices as $invoice)
                                    <li class="flex justify-between items-center bg-gray-50 p-3 rounded">
                                        <div>
                                            <div class="text-sm font-bold text-gray-800">Inv #{{ $invoice->id }}</div>
                                            <div class="text-xs text-gray-500">
                                                {{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : '' }}
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-bold text-gray-800">
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
                            <p class="text-gray-500 text-sm">No invoices found for this project.</p>
                        @endif
                    </div>
                </div>

                <!-- Attachments -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                    <div class="p-6 border-b border-gray-100 font-bold text-lg text-gray-800">
                        Attachments
                    </div>
                    <div class="p-6">
                        @if($project->attachments->count() > 0)
                            <ul class="space-y-3">
                                @foreach($project->attachments as $attachment)
                                    <li
                                        class="flex items-center justify-between p-3 bg-gray-50 rounded-lg group hover:bg-gray-100 transition-colors">
                                        <div class="flex items-center space-x-3 overflow-hidden">
                                            <div class="flex-shrink-0">
                                                @if(Str::contains($attachment->file_type, 'image'))
                                                    <i class="fas fa-image text-blue-500"></i>
                                                @elseif(Str::contains($attachment->file_type, 'pdf'))
                                                    <i class="fas fa-file-pdf text-red-500"></i>
                                                @else
                                                    <i class="fas fa-file-alt text-gray-500"></i>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <button type="button"
                                                    onclick="openPreview('{{ route('attachments.show', $attachment->id) }}', '{{ $attachment->file_name }}', '{{ $attachment->file_type }}')"
                                                    class="text-sm font-medium text-gray-900 truncate hover:text-blue-600 focus:outline-none text-left"
                                                    title="{{ $attachment->file_name }}">
                                                    {{ $attachment->file_name }}
                                                </button>
                                                <p class="text-xs text-gray-500">
                                                    {{ number_format($attachment->file_size / 1024, 1) }} KB
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            <button type="button"
                                                onclick="openPreview('{{ route('attachments.show', $attachment->id) }}', '{{ $attachment->file_name }}', '{{ $attachment->file_type }}')"
                                                class="text-gray-400 hover:text-blue-600 transition-colors p-1" title="Preview">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <a href="{{ route('attachments.show', $attachment->id) }}?download=1"
                                                class="text-gray-400 hover:text-green-600 transition-colors p-1"
                                                title="Download">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 text-sm italic">No attachments.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div id="previewModal" class="fixed inset-0 z-50 hidden overflow-y-auto" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closePreview()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="previewTitle">
                                Attachment Preview
                            </h3>
                            <div id="previewContent"
                                class="flex justify-center items-center min-h-[400px] bg-gray-100 rounded-lg overflow-hidden">
                                <!-- Content injected via JS -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        onclick="closePreview()">
                        Close
                    </button>
                    <a id="downloadLink" href="#"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Download
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openPreview(url, name, type) {
            const modal = document.getElementById('previewModal');
            const content = document.getElementById('previewContent');
            const title = document.getElementById('previewTitle');
            const downloadBtn = document.getElementById('downloadLink');

            title.innerText = name;
            downloadBtn.href = url + '?download=1';
            content.innerHTML = ''; // Clear previous

            if (type.includes('image')) {
                content.innerHTML = `<img src="${url}" class="max-w-full max-h-[70vh] object-contain" alt="${name}">`;
            } else if (type.includes('pdf')) {
                content.innerHTML = `<iframe src="${url}" class="w-full h-[70vh]" frameborder="0"></iframe>`;
            } else {
                content.innerHTML = `
                    <div class="text-center p-10">
                        <i class="fas fa-file-alt text-6xl text-gray-400 mb-4"></i>
                        <p class="text-gray-500">Preview not available for this file type.</p>
                        <p class="text-sm text-gray-400 mt-2">(${type})</p>
                    </div>
                 `;
            }

            modal.classList.remove('hidden');
        }

        function closePreview() {
            document.getElementById('previewModal').classList.add('hidden');
            document.getElementById('previewContent').innerHTML = '';
        }
    </script>
</x-app-layout>
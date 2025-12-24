<x-app-layout>
    <div class="max-w-7xl mx-auto">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
            <div>
                <div class="flex items-center gap-2 text-sm text-gray-500 mb-1">
                    <a href="{{ route('projects.index') }}" class="hover:text-blue-600">Projects</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <a href="{{ route('projects.show', $project) }}"
                        class="hover:text-blue-600">{{ $project->name }}</a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span>Documents</span>
                </div>
                <h1 class="text-2xl font-bold text-gray-800">Project Documents</h1>
                <p class="text-gray-500 mt-1">{{ $project->name }}</p>
            </div>
            <button onclick="openUploadModal()"
                class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm">
                <i class="fas fa-upload"></i>
                <span>Upload Document</span>
            </button>
        </div>

        <!-- Category Tabs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="border-b border-gray-200 bg-gray-50">
                <nav class="flex overflow-x-auto" id="categoryTabs">
                    <button onclick="showCategory('all')" data-category="all"
                        class="category-tab px-4 py-3 text-sm font-medium text-gray-600 hover:text-blue-600 border-b-2 border-transparent whitespace-nowrap active">
                        <i class="fas fa-folder mr-2"></i>All Documents
                    </button>
                    @foreach($categories as $key => $label)
                        <button onclick="showCategory('{{ $key }}')" data-category="{{ $key }}"
                            class="category-tab px-4 py-3 text-sm font-medium text-gray-600 hover:text-blue-600 border-b-2 border-transparent whitespace-nowrap">
                            <i class="fas fa-folder-open mr-2"></i>{{ $label }}
                        </button>
                    @endforeach
                </nav>
            </div>

            <!-- Documents List -->
            <div class="p-6">
                <div id="documentsContainer">
                    @php
                        $allDocs = collect();
                        foreach ($documents as $docs) {
                            $allDocs = $allDocs->merge($docs);
                        }
                    @endphp

                    @if($allDocs->isEmpty())
                        <div class="text-center py-12">
                            <i class="fas fa-folder-open text-4xl text-gray-300 mb-4"></i>
                            <p class="text-gray-500">No documents uploaded yet</p>
                        </div>
                    @else
                        <div class="grid gap-3" id="documentsList">
                            @foreach($allDocs->sortByDesc('created_at') as $doc)
                                <div class="document-item flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition group"
                                    data-category="{{ $doc->category }}" data-id="{{ $doc->id }}">
                                    <div class="flex items-center gap-4">
                                        <div
                                            class="flex-shrink-0 h-10 w-10 rounded-lg bg-white shadow-sm flex items-center justify-center">
                                            <i class="fas {{ $doc->file_icon }} text-lg"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-medium text-gray-800">{{ $doc->file_name }}</h4>
                                            <div class="flex items-center gap-3 text-xs text-gray-500 mt-1">
                                                <span
                                                    class="px-2 py-0.5 bg-blue-100 text-blue-700 rounded-full">{{ $doc->category_label }}</span>
                                                <span>{{ $doc->file_size_human }}</span>
                                                <span>by {{ $doc->uploader->name }}</span>
                                                <span>{{ $doc->created_at->format('M d, Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition">
                                        <a href="{{ route('projects.documents.download', [$project, $doc]) }}"
                                            class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                            title="Download">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        <button onclick="deleteDocument({{ $doc->id }})"
                                            class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                            title="Delete">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Upload Modal -->
    <div id="uploadModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="fixed inset-0 bg-gray-900 bg-opacity-50" onclick="closeUploadModal()"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Upload Document</h3>
                <form id="uploadForm" enctype="multipart/form-data">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Category</label>
                            <select name="category" id="uploadCategory" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">File</label>
                            <div id="dropZone"
                                class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer hover:border-blue-500 transition">
                                <i class="fas fa-cloud-upload-alt text-3xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600">Drag & drop or click to upload</p>
                                <p class="text-xs text-gray-400 mt-1">PDF, Word, Excel • Max 20MB</p>
                                <input type="file" name="file" id="uploadFile" accept=".pdf,.doc,.docx,.xls,.xlsx"
                                    class="hidden" required>
                            </div>
                            <p id="selectedFileName" class="text-sm text-gray-600 mt-2 hidden"></p>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 mt-6">
                        <button type="button" onclick="closeUploadModal()"
                            class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">
                            Cancel
                        </button>
                        <button type="submit" id="uploadBtn"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                            <i class="fas fa-upload mr-2"></i>Upload
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const projectId = {{ $project->id }};

        // Category Filter
        function showCategory(category) {
            // Update tabs
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.classList.remove('active', 'border-blue-600', 'text-blue-600');
                tab.classList.add('border-transparent');
            });
            const activeTab = document.querySelector(`[data-category="${category}"]`);
            activeTab.classList.add('active', 'border-blue-600', 'text-blue-600');

            // Filter documents
            document.querySelectorAll('.document-item').forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        // Upload Modal
        function openUploadModal() {
            document.getElementById('uploadModal').classList.remove('hidden');
        }

        function closeUploadModal() {
            document.getElementById('uploadModal').classList.add('hidden');
            document.getElementById('uploadForm').reset();
            document.getElementById('selectedFileName').classList.add('hidden');
        }

        // Drag & Drop
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('uploadFile');

        dropZone.addEventListener('click', () => fileInput.click());

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('border-blue-500', 'bg-blue-50');
        });

        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
        });

        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('border-blue-500', 'bg-blue-50');
            if (e.dataTransfer.files.length) {
                fileInput.files = e.dataTransfer.files;
                showSelectedFile(e.dataTransfer.files[0].name);
            }
        });

        fileInput.addEventListener('change', (e) => {
            if (e.target.files.length) {
                showSelectedFile(e.target.files[0].name);
            }
        });

        function showSelectedFile(filename) {
            const el = document.getElementById('selectedFileName');
            el.textContent = `Selected: ${filename}`;
            el.classList.remove('hidden');
        }

        // Upload Form
        document.getElementById('uploadForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const fileInput = document.getElementById('uploadFile');
            if (!fileInput.files.length) {
                Swal.fire('Error', 'Please select a file to upload', 'warning');
                return;
            }

            const btn = document.getElementById('uploadBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';

            const formData = new FormData(e.target);

            try {
                const response = await fetch(`/projects/${projectId}/documents`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });

                if (response.ok) {
                    const data = await response.json();
                    Swal.fire({
                        title: 'Success!',
                        text: 'Document uploaded successfully',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.reload();
                    });
                } else {
                    const error = await response.json();
                    // Handle validation errors
                    let errorMsg = 'Failed to upload document';
                    if (error.errors) {
                        errorMsg = Object.values(error.errors).flat().join('\n');
                    } else if (error.message) {
                        errorMsg = error.message;
                    }
                    Swal.fire('Error', errorMsg, 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Failed to upload document', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-upload mr-2"></i>Upload';
            }
        });

        // Delete Document
        async function deleteDocument(documentId) {
            const result = await Swal.fire({
                title: 'Delete Document?',
                text: 'This action cannot be undone.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#EF4444',
                cancelButtonColor: '#6B7280',
                confirmButtonText: 'Yes, delete it!',
            });

            if (!result.isConfirmed) return;

            try {
                const response = await fetch(`/projects/${projectId}/documents/${documentId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                if (response.ok) {
                    // Remove from DOM
                    document.querySelector(`[data-id="${documentId}"]`).remove();

                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Document has been deleted.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Error', 'Failed to delete document', 'error');
                }
            } catch (err) {
                console.error(err);
                Swal.fire('Error', 'Failed to delete document', 'error');
            }
        }
    </script>

    <style>
        .category-tab.active {
            color: #2563eb;
            border-color: #2563eb;
        }
    </style>
</x-app-layout>
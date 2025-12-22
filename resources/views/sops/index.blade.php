<x-app-layout>
    <!-- Hero Section -->
    <div
        class="max-w-7xl mx-auto bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-10 shadow-xl mb-10 rounded-3xl relative overflow-hidden">
        <!-- Decoration Circles -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-white opacity-5 rounded-full -ml-20 -mt-20"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-500 opacity-20 rounded-full -mr-20 -mb-20"></div>

        <div class="max-w-4xl mx-auto text-center relative z-10">
            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl mb-4">
                Knowledge Base
            </h1>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Find guides, policies, and standard operating procedures to help you work efficiently.
            </p>

            <div class="relative max-w-2xl mx-auto">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" onkeyup="filterCards()"
                    class="block w-full pl-10 pr-3 py-4 border-none rounded-xl leading-5 bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:text-lg shadow-xl"
                    placeholder="Search for answers...">
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <!-- New SOP Button (CEO Only) -->
        @if(auth()->user()->hasRole('CEO'))
            <div class="flex justify-end mb-8">
                <button onclick="openModal('create')"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i> Create New Article
                </button>
            </div>
        @endif

        <!-- Knowledge Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="cardsGrid">
            @foreach($sops as $sop)
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 overflow-hidden group sop-card relative flex flex-col justify-between">
                    <div class="p-8">
                        <div class="flex items-center justify-between mb-4">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-700 category-pill">
                                {{ $sop->category ?? 'General' }}
                            </span>
                            @if($sop->is_required)
                                <span class="inline-flex items-center text-red-500 text-xs font-medium"
                                    title="Required Reading">
                                    <i class="fas fa-exclamation-circle mr-1"></i> Mandatory
                                </span>
                            @endif
                        </div>
                        <h3
                            class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors sop-title">
                            {{ $sop->title }}
                        </h3>

                        <!-- Preview -->
                        <p class="text-gray-500 text-sm line-clamp-3 mb-4">
                            {{ Str::limit(strip_tags($sop->content), 100) }}
                        </p>

                        <!-- Icon (Decorative) -->
                        <div
                            class="absolute right-4 top-20 opacity-5 group-hover:opacity-10 transition-opacity transform group-hover:scale-110 pointer-events-none">
                            <i class="fas fa-file-alt text-8xl text-indigo-900"></i>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-medium">
                            Updated {{ $sop->updated_at->diffForHumans() }}
                        </span>

                        <div class="flex items-center space-x-2">
                            <!-- Action Buttons -->
                            @if(auth()->user()->hasRole('CEO'))
                                <button onclick="editSop({{ $sop->id }})"
                                    class="p-2 rounded-full text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition"
                                    title="Edit">
                                    <i class="fas fa-pencil-alt text-sm"></i>
                                </button>
                                <button onclick="deleteSop({{ $sop->id }})"
                                    class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                    title="Delete">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            @endif
                            <button onclick="viewSop({{ $sop->id }})"
                                class="p-2 rounded-full bg-white border border-gray-200 text-gray-400 hover:text-blue-600 hover:border-blue-200 shadow-sm transition">
                                <i class="fas fa-chevron-right text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($sops->isEmpty())
            <div class="text-center py-20">
                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-book-open text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No articles found</h3>
                <p class="mt-1 text-gray-500">Get started by creating a new Standard Operating Procedure.</p>
            </div>
        @endif
    </div>

    <!-- SOP Modal (Create/Edit) -->
    <div id="sopModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>
            <!-- Ghost span removed -->
            <div
                class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all w-full mx-auto sm:my-8 sm:max-w-3xl">
                <form id="sopForm" onsubmit="submitSop(event)">
                    @csrf
                    <input type="hidden" id="sopId" name="id">
                    <div class="bg-white px-8 pt-8 pb-6">
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-900" id="modalTitle">Add Article</h3>
                            <p class="text-sm text-gray-500">Create a new knowledge base article.</p>
                        </div>

                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Article
                                        Title</label>
                                    <input type="text" name="title" id="title"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3"
                                        required placeholder="e.g. General HR Policy">
                                </div>
                                <div>
                                    <label for="category"
                                        class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                                    <select name="category" id="category"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3">
                                        <option value="General">General</option>
                                        <option value="Human Resource">Human Resource</option>
                                        <option value="Engineering">Engineering</option>
                                        <option value="Finance">Finance</option>
                                        <option value="Marketing">Marketing</option>
                                        <option value="Sales">Sales</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="content"
                                    class="block text-sm font-medium text-gray-700 mb-2">Content</label>
                                <textarea name="content" id="content" rows="12"
                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                    placeholder="Write your article content here..."></textarea>
                            </div>

                            <div>
                                <label for="attachments"
                                    class="block text-sm font-medium text-gray-700 mb-2">Attachments</label>
                                <input type="file" name="attachments[]" id="attachments" multiple
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            </div>

                            <div class="flex items-center">
                                <input id="is_required" name="is_required" type="checkbox"
                                    class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="is_required" class="ml-3 block text-sm font-medium text-gray-700">
                                    Mark as Mandatory Read
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-8 py-4 flex flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">Save
                            Article</button>
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            onclick="closeModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View SOP Modal -->
    <div id="viewSopModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true"
            onclick="closeViewModal()"></div>

        <!-- Modal Positioning Container -->
        <div class="flex min-h-full items-center justify-center p-4">
            <!-- Modal Card -->
            <div
                class="relative w-full max-w-4xl transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all">
                <!-- Modal Header -->
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-4 py-5 sm:px-6 border-b border-blue-100">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0 flex-1">
                            <span id="viewSopCategory"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 mb-2">
                                Category
                            </span>
                            <h3 class="text-xl sm:text-2xl font-bold text-gray-900 break-words" id="viewSopTitle">
                                Article Title</h3>
                            <p class="text-sm text-gray-500 mt-1" id="viewSopMeta">Last updated...</p>
                        </div>
                        <button type="button"
                            class="ml-4 flex-shrink-0 text-gray-400 hover:text-gray-500 focus:outline-none"
                            onclick="closeViewModal()">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Modal Body -->
                <div class="px-4 py-6 sm:px-6 max-h-[60vh] overflow-y-auto">
                    <div class="prose prose-blue max-w-none text-gray-700 leading-relaxed text-base"
                        id="viewSopContent">
                        <!-- Content goes here -->
                    </div>

                    <!-- Attachments Section (JS populated) -->
                    <div class="mt-6 pt-6 border-t border-gray-200" id="viewSopAttachmentsContainer"
                        style="display: none;">
                        <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-3">Attachments</h4>
                        <ul class="grid grid-cols-1 gap-3" id="viewSopAttachmentsList">
                            <!-- Items injected here -->
                        </ul>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="bg-gray-50 px-4 py-4 sm:px-6 flex justify-end border-t border-gray-200">
                    <button type="button"
                        class="w-full sm:w-auto inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-2.5 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        onclick="closeViewModal()">Close</button>
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

    <style>
        .ck-content ul,
        #viewSopContent ul {
            list-style-type: disc !important;
            list-style-type: disc !important;
            padding-left: 1.25rem !important;
            /* Mobile friendly */
        }

        @media (min-width: 640px) {

            .ck-content ul,
            #viewSopContent ul {
                padding-left: 2rem !important;
            }
        }
        }

        .ck-content ol,
        #viewSopContent ol {
            list-style-type: decimal !important;
            list-style-type: decimal !important;
            padding-left: 1.25rem !important;
            /* Mobile friendly */
        }

        @media (min-width: 640px) {

            .ck-content ol,
            #viewSopContent ol {
                padding-left: 2rem !important;
            }
        }
        }

        .ck-content h2,
        #viewSopContent h2 {
            font-size: 1.5em;
            font-weight: bold;
            margin-top: 1em;
            margin-bottom: 0.5em;
        }

        .ck-content h3,
        #viewSopContent h3 {
            font-size: 1.25em;
            font-weight: bold;
            margin-top: 1em;
            margin-bottom: 0.5em;
        }

        .ck-content p,
        #viewSopContent p {
            margin-bottom: 0.75em;
        }

        .ck-content blockquote,
        #viewSopContent blockquote {
            border-left: 4px solid #e5e7eb;
            padding-left: 1rem;
            font-style: italic;
        }
    </style>
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        let editorInstance;

        // Initialize CKEditor
        ClassicEditor
            .create(document.querySelector('#content'), {
                toolbar: ['heading', '|', 'bold', 'italic', 'link', 'bulletedList', 'numberedList', 'blockQuote', 'insertTable', '|', 'undo', 'redo']
            })
            .then(editor => {
                editorInstance = editor;
            })
            .catch(error => {
                console.error(error);
            });

        // Search Functionality
        function filterCards() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const grid = document.getElementById('cardsGrid');
            const cards = grid.getElementsByClassName('sop-card');

            for (let i = 0; i < cards.length; i++) {
                const title = cards[i].getElementsByClassName('sop-title')[0];
                const category = cards[i].getElementsByClassName('category-pill')[0];
                const txtValue = (title.textContent || title.innerText) + " " + (category.textContent || category.innerText);

                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }

        // Modal Logic
        function openModal(mode, data = null) {
            document.getElementById('sopModal').classList.remove('hidden');
            const form = document.getElementById('sopForm');
            const title = document.getElementById('modalTitle');

            if (mode === 'create') {
                title.innerText = 'Add New Article';
                form.reset();
                document.getElementById('sopId').value = '';
                document.getElementById('category').value = 'General';
                document.getElementById('is_required').checked = false;
                if (editorInstance) {
                    editorInstance.setData('');
                }
            } else if (mode === 'edit' && data) {
                title.innerText = 'Edit Article';
                document.getElementById('sopId').value = data.id;
                document.getElementById('title').value = data.title;
                // document.getElementById('content').value = data.content; // Handled by editor
                document.getElementById('category').value = data.category || 'General';
                document.getElementById('is_required').checked = data.is_required == 1; // == 1 handles true/1
                if (editorInstance) {
                    editorInstance.setData(data.content || '');
                }
            }
        }

        function closeModal() {
            document.getElementById('sopModal').classList.add('hidden');
        }

        function closeViewModal() {
            document.getElementById('viewSopModal').classList.add('hidden');
        }

        async function submitSop(event) {
            event.preventDefault();

            // Sync CKEditor data
            if (editorInstance) {
                const data = editorInstance.getData();
                if (!data) {
                    Swal.fire('Error', 'Content cannot be empty', 'error');
                    return;
                }
                document.querySelector('#content').value = data;
            }

            const form = document.getElementById('sopForm');
            const formData = new FormData(form);
            const id = formData.get('id');
            const url = id ? `/sops/${id}` : '/sops';

            // For updates (PUT), Laravel needs standard POST with _method field for file uploads
            if (id) {
                formData.append('_method', 'PUT');
            }

            // Handle checkbox (send 1 or 0)
            const isRequired = document.getElementById('is_required').checked ? '1' : '0';
            formData.set('is_required', isRequired);

            try {
                const response = await fetch(url, {
                    method: 'POST', // Always POST for file uploads
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                });

                if (!response.ok) {
                    const error = await response.json();
                    if (response.status === 403) throw new Error('Unauthorized');
                    throw new Error(error.message || 'Network response was not ok');
                }
                const result = await response.json();

                closeModal();
                Swal.fire({
                    title: 'Success!',
                    text: result.message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', error.message || 'Unauthorized or Error Occurred', 'error');
            }
        }

        async function editSop(id) {
            try {
                const response = await fetch(`/sops/${id}/edit`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                if (response.status === 403) {
                    Swal.fire('Error', 'Unauthorized', 'error');
                    return;
                }
                const data = await response.json();
                openModal('edit', data);
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Could not fetch SOP data', 'error');
            }
        }

        async function viewSop(id) {
            try {
                const response = await fetch(`/sops/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();

                document.getElementById('viewSopTitle').innerText = data.title;
                document.getElementById('viewSopCategory').innerText = data.category || 'General';
                document.getElementById('viewSopContent').innerHTML = data.content; // innerHTML for rich text components
                document.getElementById('viewSopMeta').innerText = 'Last updated ' + new Date(data.updated_at).toLocaleDateString();

                // Attachments
                const attachmentsContainer = document.getElementById('viewSopAttachmentsContainer');
                const attachmentsList = document.getElementById('viewSopAttachmentsList');
                attachmentsList.innerHTML = ''; // Clear prev

                if (data.attachments && data.attachments.length > 0) {
                    attachmentsContainer.style.display = 'block';
                    data.attachments.forEach(file => {
                        const li = document.createElement('li');
                        li.className = 'flex items-center justify-between p-3 bg-gray-50 rounded-xl border border-gray-100 hover:border-blue-200 transition-colors';

                        let iconClass = 'fa-file-alt text-gray-400';
                        if (file.file_type && file.file_type.includes('image')) iconClass = 'fa-image text-blue-500';
                        else if (file.file_type && file.file_type.includes('pdf')) iconClass = 'fa-file-pdf text-red-500';

                        const filePath = `/attachments/${file.id}`;
                        li.innerHTML = `
                            <div class="flex items-center space-x-3 overflow-hidden">
                                <i class="fas ${iconClass} text-lg"></i>
                                <div class="min-w-0">
                                    <button type="button" onclick="openPreview('${filePath}', '${file.file_name}', '${file.file_type}')"
                                        class="text-sm font-medium text-gray-700 truncate hover:text-blue-600 text-left focus:outline-none" title="${file.file_name}">
                                        ${file.file_name}
                                    </button>
                                    <p class="text-xs text-gray-500">${(file.file_size / 1024).toFixed(1)} KB</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <button type="button" onclick="openPreview('${filePath}', '${file.file_name}', '${file.file_type}')"
                                    class="text-gray-400 hover:text-blue-600 p-2" title="Preview">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="${filePath}?download=1" class="text-gray-400 hover:text-green-600 p-2" title="Download">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        `;
                        attachmentsList.appendChild(li);
                    });
                } else {
                    attachmentsContainer.style.display = 'none';
                }

                document.getElementById('viewSopModal').classList.remove('hidden');
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Could not fetch SOP data', 'error');
            }
        }

        function deleteSop(id) {
            Swal.fire({
                title: 'Delete Article?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/sops/${id}`, { // Fixed spacing
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => {
                            if (response.status === 403) throw new Error('Unauthorized');
                            return response.json();
                        })
                        .then(data => {
                            Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                        })
                        .catch(error => Swal.fire('Error', 'Could not delete SOP (Unauthorized?)', 'error'));
                }
            })
        }

        // Preview Modal Logic
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
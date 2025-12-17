<x-app-layout>
    <!-- Hero Section -->
    <div
        class="max-w-7xl mx-auto bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-10 shadow-xl mb-10 rounded-3xl relative overflow-hidden">
        <!-- Decoration Circles -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-white opacity-5 rounded-full -ml-20 -mt-20"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-500 opacity-20 rounded-full -mr-20 -mb-20"></div>

        <div class="max-w-4xl mx-auto text-center relative z-10">
            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl mb-4">
                Projects
            </h1>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Manage your projects, track progress, and monitor budgets.
            </p>

            <div class="relative max-w-2xl mx-auto">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" onkeyup="filterCards()"
                    class="block w-full pl-10 pr-3 py-4 border-none rounded-xl leading-5 bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:text-lg shadow-xl"
                    placeholder="Search projects...">
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <!-- Add Button -->
        <div class="flex justify-end mb-8">
            <button onclick="openModal('create')"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i> Add Project
            </button>
        </div>

        <!-- Projects Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="cardsGrid">
            @foreach($projects as $project)
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100 flex flex-col h-full project-card group relative">
                    <div class="p-6 flex-1">
                        <div class="flex justify-between items-start mb-4">
                            <span
                                class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 uppercase tracking-wide">
                                {{ $project->client->name }}
                            </span>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold uppercase tracking-wider
                                                {{ $project->status === 'Completed' ? 'bg-green-100 text-green-800' : ($project->status === 'In Progress' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $project->status }}
                            </span>
                        </div>

                        <h3
                            class="text-xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors project-title">
                            {{ $project->name }}
                        </h3>

                        <p class="text-gray-500 text-sm line-clamp-3 mb-4">
                            {{ $project->description ?? 'No description provided.' }}
                        </p>

                        <div class="mt-4 flex items-center justify-between text-sm text-gray-500">
                            <div>
                                <i class="fas fa-money-bill-wave mr-1 text-green-500"></i>
                                Rp {{ number_format($project->budget, 0, ',', '.') }}
                            </div>
                            <div>
                                <i class="fas fa-calendar-alt mr-1 text-red-400"></i>
                                {{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('M d, Y') : 'No Deadline' }}
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-xs text-gray-400 font-medium">
                            {{ $project->active ? 'Active' : 'Inactive' }}
                        </span>

                        <div class="flex items-center space-x-2">
                            <a href="{{ route('projects.show', $project->id) }}"
                                class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition"
                                title="View Details">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                            <button onclick="editProject({{ $project->id }})"
                                class="p-2 rounded-full text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 transition"
                                title="Edit">
                                <i class="fas fa-pencil-alt text-sm"></i>
                            </button>
                            <button onclick="deleteProject({{ $project->id }})"
                                class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                                title="Delete">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if($projects->isEmpty())
            <div class="text-center py-20">
                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-project-diagram text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No projects found</h3>
                <p class="mt-1 text-gray-500">Get started by creating a new project.</p>
            </div>
        @endif
    </div>

    <!-- Project Modal -->
    <div id="projectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                <form id="projectForm" onsubmit="submitProject(event)">
                    @csrf
                    <input type="hidden" id="projectId" name="id">
                    <div class="bg-white px-8 pt-8 pb-6">
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-900" id="modalTitle">Add Project</h3>
                            <p class="text-sm text-gray-500">Create or edit a project.</p>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Project
                                    Name</label>
                                <input type="text" name="name" id="name"
                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3"
                                    required>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="client_id"
                                        class="block text-sm font-medium text-gray-700 mb-2">Client</label>
                                    <select name="client_id" id="client_id"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3"
                                        required>
                                        <option value="">Select Client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}">{{ $client->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label for="status"
                                        class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status" id="status"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3">
                                        <option value="Pending">Pending</option>
                                        <option value="In Progress">In Progress</option>
                                        <option value="Completed">Completed</option>
                                        <option value="Cancelled">Cancelled</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="budget" class="block text-sm font-medium text-gray-700 mb-2">Budget
                                        (Rp)</label>
                                    <input type="number" step="0.01" name="budget" id="budget"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3">
                                </div>
                                <div>
                                    <label for="deadline"
                                        class="block text-sm font-medium text-gray-700 mb-2">Deadline</label>
                                    <input type="date" name="deadline" id="deadline"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3">
                                </div>
                            </div>

                            <div>
                                <label for="description"
                                    class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                                <textarea name="description" id="description" rows="3"
                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                            </div>

                            <div class="flex items-center">
                                <input id="active" name="active" type="checkbox" value="1"
                                    class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                <label for="active" class="ml-3 block text-sm font-medium text-gray-700">
                                    Active (Show on Dashboard)
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-8 py-4 flex flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">Save
                            Project</button>
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            onclick="closeModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function filterCards() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const grid = document.getElementById('cardsGrid');
            const cards = grid.getElementsByClassName('project-card');

            for (let i = 0; i < cards.length; i++) {
                const title = cards[i].getElementsByClassName('project-title')[0];
                const txtValue = title.textContent || title.innerText;

                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }

        function openModal(mode, data = null) {
            document.getElementById('projectModal').classList.remove('hidden');
            const form = document.getElementById('projectForm');
            const title = document.getElementById('modalTitle');

            if (mode === 'create') {
                title.innerText = 'Add Project';
                form.reset();
                document.getElementById('projectId').value = '';
                document.getElementById('active').checked = true;
            } else if (mode === 'edit' && data) {
                title.innerText = 'Edit Project';
                document.getElementById('projectId').value = data.id;
                document.getElementById('name').value = data.name;
                document.getElementById('client_id').value = data.client_id;
                document.getElementById('status').value = data.status;
                document.getElementById('budget').value = data.budget;
                document.getElementById('deadline').value = data.deadline ? data.deadline.substring(0, 10) : '';
                document.getElementById('description').value = data.description || '';
                document.getElementById('active').checked = data.active == 1;
            }
        }

        function closeModal() {
            document.getElementById('projectModal').classList.add('hidden');
        }

        async function submitProject(event) {
            event.preventDefault();
            const form = document.getElementById('projectForm');
            const formData = new FormData(form);
            const id = formData.get('id');
            const url = id ? `/projects/${id}` : '/projects';

            const data = Object.fromEntries(formData.entries());
            data.active = document.getElementById('active').checked;

            try {
                const response = await fetch(url, {
                    method: id ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) throw new Error('Network response was not ok');
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
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        }

        async function editProject(id) {
            try {
                const response = await fetch(`/projects/${id}/edit`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                openModal('edit', data);
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Could not fetch project data', 'error');
            }
        }

        function deleteProject(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#d33', // Both red? Defaults ok
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/projects/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            Swal.fire('Deleted!', data.message, 'success').then(() => location.reload());
                        })
                        .catch(error => Swal.fire('Error', 'Could not delete project', 'error'));
                }
            })
        }
    </script>
</x-app-layout>
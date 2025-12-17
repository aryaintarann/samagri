<x-app-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800  leading-tight">
                {{ __('Projects') }}
            </h2>
            <button onclick="openModal('create')"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i> Add Project
            </button>
        </div>

        <div class="bg-white  overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Project Name</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Client</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Budget</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($projects as $project)
                            <tr class="hover:bg-gray-50 :bg-gray-700 transition">
                                <td
                                    class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap font-semibold">
                                        {{ $project->name }}</p>
                                    @if($project->active)
                                        <span class="text-xs text-green-500">Active</span>
                                    @else
                                        <span class="text-xs text-gray-500">Inactive</span>
                                    @endif
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap">
                                        {{ $project->client->name }}</p>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <span
                                        class="relative inline-block px-3 py-1 font-semibold leading-tight 
                                        {{ $project->status === 'Completed' ? 'text-green-900' : ($project->status === 'In Progress' ? 'text-blue-900' : 'text-yellow-900') }}">
                                        <span aria-hidden
                                            class="absolute inset-0 opacity-50 rounded-full 
                                            {{ $project->status === 'Completed' ? 'bg-green-200' : ($project->status === 'In Progress' ? 'bg-blue-200' : 'bg-yellow-200') }}"></span>
                                        <span class="relative">{{ $project->status }}</span>
                                    </span>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap">
                                        ${{ number_format($project->budget, 2) }}</p>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <a href="{{ route('projects.show', $project->id) }}"
                                        class="text-blue-500 hover:text-blue-700 mr-3 transition"><i
                                            class="fas fa-eye"></i></a>
                                    <button onclick="editProject({{ $project->id }})"
                                        class="text-yellow-500 hover:text-yellow-700 mr-3 transition"><i
                                            class="fas fa-edit"></i></button>
                                    <button onclick="deleteProject({{ $project->id }})"
                                        class="text-red-500 hover:text-red-700 transition"><i
                                            class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($projects->isEmpty())
                    <div class="text-center py-4 text-gray-500">No projects found.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Project Modal -->
    <div id="projectModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white  rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="projectForm" onsubmit="submitProject(event)">
                    @csrf
                    <input type="hidden" id="projectId" name="id">
                    <div class="bg-white  px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 "
                                    id="modalTitle">Add Project</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="name"
                                            class="block text-sm font-medium text-gray-700 ">Project
                                            Name</label>
                                        <input type="text" name="name" id="name"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label for="client_id"
                                            class="block text-sm font-medium text-gray-700 ">Client</label>
                                        <select name="client_id" id="client_id"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                            <option value="">Select Client</option>
                                            @foreach($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="status"
                                            class="block text-sm font-medium text-gray-700 ">Status</label>
                                        <select name="status" id="status"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="Pending">Pending</option>
                                            <option value="In Progress">In Progress</option>
                                            <option value="Completed">Completed</option>
                                            <option value="Cancelled">Cancelled</option>
                                        </select>
                                    </div>
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label for="budget"
                                                class="block text-sm font-medium text-gray-700 ">Budget</label>
                                            <input type="number" step="0.01" name="budget" id="budget"
                                                class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        </div>
                                        <div>
                                            <label for="deadline"
                                                class="block text-sm font-medium text-gray-700 ">Deadline</label>
                                            <input type="date" name="deadline" id="deadline"
                                                class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                        </div>
                                    </div>
                                    <div>
                                        <label for="description"
                                            class="block text-sm font-medium text-gray-700 ">Description</label>
                                        <textarea name="description" id="description" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                                    </div>
                                    <div class="flex items-center">
                                        <input id="active" name="active" type="checkbox" value="1"
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                                        <label for="active" class="ml-2 block text-sm text-gray-900 ">
                                            Active (Show on Dashboard)
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50  px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">Save</button>
                        <button type="button"
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300  shadow-sm px-4 py-2 bg-white  text-base font-medium text-gray-700  hover:bg-gray-50 :bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                            onclick="closeModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openModal(mode, data = null) {
            document.getElementById('projectModal').classList.remove('hidden');
            const form = document.getElementById('projectForm');
            const title = document.getElementById('modalTitle');

            if (mode === 'create') {
                title.innerText = 'Add Project';
                form.reset();
                document.getElementById('projectId').value = '';
                document.getElementById('active').checked = true; // Default active
            } else if (mode === 'edit' && data) {
                title.innerText = 'Edit Project';
                document.getElementById('projectId').value = data.id;
                document.getElementById('name').value = data.name;
                document.getElementById('client_id').value = data.client_id;
                document.getElementById('status').value = data.status;
                document.getElementById('budget').value = data.budget;
                // Date format might need adjustment YYYY-MM-DD
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

            // Handle Checkbox: if unchecked it's not in formData, so we need to handle it or relying on backend handling 'has'. 
            // My Controller checks has('active'), but standard HTML form submit doesn't send unchecked.
            // FormData DOES NOT contain unchecked checkboxes. 
            // So I should manually append it? Or rely on default false?
            // Actually, if we send via JSON, we construct the object.

            const data = Object.fromEntries(formData.entries());
            data.active = document.getElementById('active').checked; // Explicitly set boolean

            try {
                const response = await fetch(url, {
                    method: id ? 'PUT' : 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                if (!response.ok) throw new Error('Network response was not ok');
                const result = await response.json();

                closeModal();
                Swal.fire('Success', result.message, 'success').then(() => {
                    location.reload();
                });
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        }

        async function editProject(id) {
            try {
                const response = await fetch(`/projects/${id}/edit`, { // Using /edit route or directly API if available. 
                    // Wait, usually resource controller returns VIEW for edit. 
                    // I modified ProjectController to return JSON if ajax request on edit()
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
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/projects/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
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
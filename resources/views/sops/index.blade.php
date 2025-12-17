<x-app-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800  leading-tight">
                {{ __('Standard Operating Procedures') }}
            </h2>
            @if(auth()->user()->role === 'CEO')
                <button onclick="openModal('create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i> Add SOP
                </button>
            @endif
        </div>

        <div class="bg-white  overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Title</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Created By</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Date</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sops as $sop)
                            <tr class="hover:bg-gray-50 :bg-gray-700 transition">
                                <td class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap font-bold">
                                        {{ $sop->title }}
                                    </p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap">{{ $sop->creator->name }}
                                    </p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap">
                                        {{ $sop->created_at->format('M d, Y') }}
                                    </p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <button onclick="viewSop({{ $sop->id }})"
                                        class="text-blue-500 hover:text-blue-700 mr-3 transition"><i
                                            class="fas fa-eye"></i></button>
                                    @if(auth()->user()->role === 'CEO')
                                        <button onclick="editSop({{ $sop->id }})"
                                            class="text-yellow-500 hover:text-yellow-700 mr-3 transition"><i
                                                class="fas fa-edit"></i></button>
                                        <button onclick="deleteSop({{ $sop->id }})"
                                            class="text-red-500 hover:text-red-700 transition"><i
                                                class="fas fa-trash"></i></button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($sops->isEmpty())
                    <div class="text-center py-4 text-gray-500">No SOPs found.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- SOP Modal (Create/Edit) -->
    <div id="sopModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white  rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <form id="sopForm" onsubmit="submitSop(event)">
                    @csrf
                    <input type="hidden" id="sopId" name="id">
                    <div class="bg-white  px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 " id="modalTitle">Add SOP</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="title"
                                            class="block text-sm font-medium text-gray-700 ">Title</label>
                                        <input type="text" name="title" id="title"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label for="content"
                                            class="block text-sm font-medium text-gray-700 ">Content</label>
                                        <textarea name="content" id="content" rows="10"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required></textarea>
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

    <!-- View SOP Modal -->
    <div id="viewSopModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeViewModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white  rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
                <div class="bg-white  px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-2xl leading-6 font-bold text-gray-900  mb-4" id="viewSopTitle">
                    </h3>
                    <div class="prose  max-w-none text-gray-700  whitespace-pre-wrap" id="viewSopContent"></div>
                </div>
                <div class="bg-gray-50  px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300  shadow-sm px-4 py-2 bg-white  text-base font-medium text-gray-700  hover:bg-gray-50 :bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
                        onclick="closeViewModal()">Close</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        function openModal(mode, data = null) {
            document.getElementById('sopModal').classList.remove('hidden');
            const form = document.getElementById('sopForm');
            const title = document.getElementById('modalTitle');

            if (mode === 'create') {
                title.innerText = 'Add SOP';
                form.reset();
                document.getElementById('sopId').value = '';
            } else if (mode === 'edit' && data) {
                title.innerText = 'Edit SOP';
                document.getElementById('sopId').value = data.id;
                document.getElementById('title').value = data.title;
                document.getElementById('content').value = data.content;
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
            const form = document.getElementById('sopForm');
            const formData = new FormData(form);
            const id = formData.get('id');
            const url = id ? `/sops/${id}` : '/sops';

            const data = Object.fromEntries(formData.entries());

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

                if (!response.ok) throw new Error('Network response was not ok'); // 403 will throw here
                const result = await response.json();

                closeModal();
                Swal.fire('Success', result.message, 'success').then(() => {
                    location.reload();
                });
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Unauthorized or Error Occurred', 'error');
            }
        }

        async function editSop(id) {
            try {
                const response = await fetch(`/sops/${id}/edit`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
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
                document.getElementById('viewSopContent').innerText = data.content; // Use innerText to safe escape or innerHTML if trusted
                document.getElementById('viewSopModal').classList.remove('hidden');
            } catch (error) {
                Swal.fire('Error', 'Could not fetch SOP data', 'error');
            }
        }


        function deleteSop(id) {
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
                    fetch(`/sops/${id}`, {
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
    </script>
</x-app-layout>
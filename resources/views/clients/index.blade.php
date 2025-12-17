<x-app-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800  leading-tight">
                {{ __('Clients') }}
            </h2>
            @if(in_array(auth()->user()->role, ['CEO', 'Marketing']))
                <button onclick="openModal('create')"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i> Add Client
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
                                Name</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Company</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Email</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Phone</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clients as $client)
                            <tr class="hover:bg-gray-50 :bg-gray-700 transition">
                                <td class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap font-semibold">
                                        {{ $client->name }}
                                    </p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap">
                                        {{ $client->company ?? '-' }}
                                    </p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap">{{ $client->email }}</p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap">
                                        {{ $client->phone ?? '-' }}
                                    </p>
                                </td>
                                <td class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <button onclick="viewClient({{ $client->id }})"
                                        class="text-blue-500 hover:text-blue-700 mr-3 transition"><i
                                            class="fas fa-eye"></i></button>
                                    @if(in_array(auth()->user()->role, ['CEO', 'Marketing']))
                                        <button onclick="editClient({{ $client->id }})"
                                            class="text-yellow-500 hover:text-yellow-700 mr-3 transition"><i
                                                class="fas fa-edit"></i></button>
                                        <button onclick="deleteClient({{ $client->id }})"
                                            class="text-red-500 hover:text-red-700 transition"><i
                                                class="fas fa-trash"></i></button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($clients->isEmpty())
                    <div class="text-center py-4 text-gray-500">No clients found.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Client Modal -->
    <div id="clientModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white  rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="clientForm" onsubmit="submitClient(event)">
                    @csrf
                    <input type="hidden" id="clientId" name="id">
                    <div class="bg-white  px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 " id="modalTitle">Add Client</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="name" class="block text-sm font-medium text-gray-700 ">Name</label>
                                        <input type="text" name="name" id="name"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label for="email"
                                            class="block text-sm font-medium text-gray-700 ">Email</label>
                                        <input type="email" name="email" id="email"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label for="company"
                                            class="block text-sm font-medium text-gray-700 ">Company</label>
                                        <input type="text" name="company" id="company"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="phone"
                                            class="block text-sm font-medium text-gray-700 ">Phone</label>
                                        <input type="text" name="phone" id="phone"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                    </div>
                                    <div>
                                        <label for="address"
                                            class="block text-sm font-medium text-gray-700 ">Address</label>
                                        <textarea name="address" id="address" rows="3"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
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
            document.getElementById('clientModal').classList.remove('hidden');
            const form = document.getElementById('clientForm');
            const title = document.getElementById('modalTitle');

            if (mode === 'create') {
                title.innerText = 'Add Client';
                form.reset();
                document.getElementById('clientId').value = '';
            } else if (mode === 'edit' && data) {
                title.innerText = 'Edit Client';
                document.getElementById('clientId').value = data.id;
                document.getElementById('name').value = data.name;
                document.getElementById('email').value = data.email;
                document.getElementById('company').value = data.company || '';
                document.getElementById('phone').value = data.phone || '';
                document.getElementById('address').value = data.address || '';
            }
        }

        function closeModal() {
            document.getElementById('clientModal').classList.add('hidden');
        }

        async function submitClient(event) {
            event.preventDefault();
            const form = document.getElementById('clientForm');
            const formData = new FormData(form);
            const id = formData.get('id');
            const url = id ? `/clients/${id}` : '/clients';
            const method = id ? 'PUT' : 'POST'; // Form method spoofing needed for Laravel usually? No, Axios handles it if we send _method or just use put. But FormData is easier with POST + _method.

            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(url, {
                    method: id ? 'PUT' : 'POST', // Fetch PUT works if endpoint accepts it. Laravel Resource does.
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
                Swal.fire('Success', result.message, 'success').then(() => {
                    location.reload(); // Simple reload for now
                });
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        }

        async function editClient(id) {
            try {
                const response = await fetch(`/clients/${id}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                const data = await response.json();
                openModal('edit', data);
            } catch (error) {
                Swal.fire('Error', 'Could not fetch client data', 'error');
            }
        }

        function deleteClient(id) {
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
                    fetch(`/clients/${id}`, {
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
                        .catch(error => Swal.fire('Error', 'Could not delete client', 'error'));
                }
            })
        }

        function viewClient(id) {
            // For now just reuse edit modal but maybe disabled? Or simple alert
            editClient(id); // Lazy "View" = Edit for now since inputs are same.
        }
    </script>
</x-app-layout>
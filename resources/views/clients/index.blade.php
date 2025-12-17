<x-app-layout>
    <!-- Hero Section -->
    <div class="max-w-7xl mx-auto bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-10 shadow-xl mb-10 rounded-3xl relative overflow-hidden">
        <!-- Decoration Circles -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-white opacity-5 rounded-full -ml-20 -mt-20"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-500 opacity-20 rounded-full -mr-20 -mb-20"></div>

        <div class="max-w-4xl mx-auto text-center relative z-10">
            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl mb-4">
                Clients
            </h1>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Manage your client relationships and contact details.
            </p>
            
            <div class="relative max-w-2xl mx-auto">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" onkeyup="filterCards()" 
                    class="block w-full pl-10 pr-3 py-4 border-none rounded-xl leading-5 bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:text-lg shadow-xl" 
                    placeholder="Search clients...">
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <!-- Add Button (Role Check) -->
        @if(in_array(auth()->user()->role, ['CEO', 'Marketing']))
            <div class="flex justify-end mb-8">
                <button onclick="openModal('create')"
                    class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition transform hover:scale-105">
                    <i class="fas fa-plus mr-2"></i> Add Client
                </button>
            </div>
        @endif

        <!-- Clients Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="cardsGrid">
            @foreach($clients as $client)
                <div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100 flex flex-col h-full client-card group relative">
                    <div class="p-6 flex-1">
                        <div class="flex items-center mb-4">
                            <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 text-xl font-bold mr-4">
                                {{ substr($client->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors client-name">
                                    {{ $client->name }}
                                </h3>
                                <p class="text-sm text-gray-500 font-medium truncate client-company">{{ $client->company }}</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-envelope w-6 text-center text-gray-400"></i>
                                <span class="truncate">{{ $client->email }}</span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600">
                                <i class="fas fa-phone w-6 text-center text-gray-400"></i>
                                <span>{{ $client->phone ?? 'No Phone' }}</span>
                            </div>
                             <div class="flex items-start text-sm text-gray-600">
                                <i class="fas fa-map-marker-alt w-6 text-center text-gray-400 mt-1"></i>
                                <span class="line-clamp-2">{{ $client->address ?? 'No Address' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end space-x-2">
                        <!-- Action Buttons -->
                        <button onclick="viewClient({{ $client->id }})" class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition" title="View Details">
                            <i class="fas fa-eye text-sm"></i>
                        </button>
                        @if(in_array(auth()->user()->role, ['CEO', 'Marketing']))
                            <button onclick="editClient({{ $client->id }})" class="p-2 rounded-full text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 transition" title="Edit">
                                <i class="fas fa-pencil-alt text-sm"></i>
                            </button>
                            <button onclick="deleteClient({{ $client->id }})" class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 transition" title="Delete">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        @if($clients->isEmpty())
             <div class="text-center py-20">
                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-users text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No clients found</h3>
                <p class="mt-1 text-gray-500">Add a new client to get started.</p>
            </div>
        @endif
    </div>

    <!-- Client Modal -->
    <div id="clientModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="clientForm" onsubmit="submitClient(event)">
                    @csrf
                    <input type="hidden" id="clientId" name="id">
                    <div class="bg-white px-8 pt-8 pb-6">
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-900" id="modalTitle">Add Client</h3>
                            <p class="text-sm text-gray-500">Enter client details below.</p>
                        </div>
                        
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                                <input type="text" name="name" id="name" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3" required>
                            </div>
                            
                            <div>
                                <label for="company" class="block text-sm font-medium text-gray-700 mb-2">Company</label>
                                <input type="text" name="company" id="company" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3">
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                    <input type="email" name="email" id="email" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3" required>
                                </div>
                                <div>
                                    <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                    <input type="text" name="phone" id="phone" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3">
                                </div>
                            </div>
                            
                            <div>
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                                <textarea name="address" id="address" rows="3" class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-50 px-8 py-4 flex flex-row-reverse">
                        <button type="submit" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">Save Client</button>
                        <button type="button" class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm" onclick="closeModal()">Cancel</button>
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
            const cards = grid.getElementsByClassName('client-card');

            for (let i = 0; i < cards.length; i++) {
                const name = cards[i].getElementsByClassName('client-name')[0];
                const company = cards[i].getElementsByClassName('client-company')[0];
                const txtValue = (name.textContent || name.innerText) + " " + (company.textContent || company.innerText);
                
                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }

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

        async function editClient(id) {
            try {
                const response = await fetch(`/clients/${id}`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
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
                confirmButtonColor: '#d33',
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
            editClient(id); // Re-using edit for now
        }
    </script>
</x-app-layout>
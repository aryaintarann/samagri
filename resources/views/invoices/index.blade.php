<x-app-layout>
    <!-- Hero Section -->
    <div
        class="max-w-7xl mx-auto bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-10 shadow-xl mb-10 rounded-3xl relative overflow-hidden">
        <!-- Decoration Circles -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-white opacity-5 rounded-full -ml-20 -mt-20"></div>
        <div class="absolute bottom-0 right-0 w-96 h-96 bg-indigo-500 opacity-20 rounded-full -mr-20 -mb-20"></div>

        <div class="max-w-4xl mx-auto text-center relative z-10">
            <h1 class="text-4xl font-extrabold tracking-tight sm:text-5xl mb-4">
                Invoices
            </h1>
            <p class="text-xl text-blue-100 mb-8 max-w-2xl mx-auto">
                Track payments, manage billing, and generate financial reports.
            </p>

            <div class="relative max-w-2xl mx-auto">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" onkeyup="filterCards()"
                    class="block w-full pl-10 pr-3 py-4 border-none rounded-xl leading-5 bg-white text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-blue-300 sm:text-lg shadow-xl"
                    placeholder="Search invoices...">
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-12">
        <!-- Add Button -->
        <div class="flex justify-end mb-8">
            <button onclick="openModal('create')"
                class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-full shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i> Create Invoice
            </button>
        </div>

        <!-- Invoices Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="cardsGrid">
            @foreach($invoices as $invoice)
                <!-- Card -->
                <div
                    class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-shadow duration-300 overflow-hidden border border-gray-100 flex flex-col h-full invoice-card group relative">
                    <div class="p-6 flex-1">
                        <div class="flex justify-between items-start mb-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Invoice #</span>
                                <h3
                                    class="text-lg font-bold text-gray-900 group-hover:text-blue-600 transition-colors invoice-number">
                                    {{ $invoice->invoice_number }}
                                </h3>
                            </div>
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold uppercase tracking-wider
                                            {{ $invoice->status === 'Paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $invoice->status }}
                            </span>
                        </div>

                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-500">Project</h4>
                            <p class="text-base font-semibold text-gray-900 invoice-project">{{ $invoice->project->name }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $invoice->project->client->name }}</p>
                        </div>

                        <div class="flex items-baseline mb-4">
                            <span class="text-2xl font-extrabold text-gray-900">
                                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-calendar bg-gray-100 p-1.5 rounded-full mr-2"></i>
                            Due: {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end space-x-2">
                        <a href="{{ route('invoices.download', $invoice->id) }}"
                            class="p-2 rounded-full text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition"
                            title="Download PDF">
                            <i class="fas fa-download text-sm"></i>
                        </a>
                        <button onclick="editInvoice({{ $invoice->id }})"
                            class="p-2 rounded-full text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 transition"
                            title="Edit">
                            <i class="fas fa-pencil-alt text-sm"></i>
                        </button>
                        <button onclick="deleteInvoice({{ $invoice->id }})"
                            class="p-2 rounded-full text-gray-400 hover:text-red-600 hover:bg-red-50 transition"
                            title="Delete">
                            <i class="fas fa-trash text-sm"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        @if($invoices->isEmpty())
            <div class="text-center py-20">
                <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gray-100 mb-4">
                    <i class="fas fa-file-invoice-dollar text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900">No invoices found</h3>
                <p class="mt-1 text-gray-500">Create a new invoice to get started.</p>
            </div>
        @endif
    </div>

    <!-- Invoice Modal -->
    <div id="invoiceModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="invoiceForm" onsubmit="submitInvoice(event)">
                    @csrf
                    <input type="hidden" id="invoiceId" name="id">
                    <div class="bg-white px-8 pt-8 pb-6">
                        <div class="mb-6">
                            <h3 class="text-2xl font-bold text-gray-900" id="modalTitle">Create Invoice</h3>
                            <p class="text-sm text-gray-500">Enter invoice details.</p>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <label for="project_id"
                                    class="block text-sm font-medium text-gray-700 mb-2">Project</label>
                                <select name="project_id" id="project_id"
                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3"
                                    required>
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->name }}
                                            ({{ $project->client->name }})</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">Amount
                                        (Rp)</label>
                                    <input type="number" step="0.01" name="amount" id="amount"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3"
                                        required>
                                </div>
                                <div>
                                    <label for="status"
                                        class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <select name="status" id="status"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3">
                                        <option value="Pending">Pending</option>
                                        <option value="Paid">Paid</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">Due
                                    Date</label>
                                <input type="date" name="due_date" id="due_date"
                                    class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-8 py-4 flex flex-row-reverse">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-6 py-3 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">Save
                            Invoice</button>
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
            const cards = grid.getElementsByClassName('invoice-card');

            for (let i = 0; i < cards.length; i++) {
                const number = cards[i].getElementsByClassName('invoice-number')[0];
                const project = cards[i].getElementsByClassName('invoice-project')[0];
                const txtValue = (number.textContent || number.innerText) + " " + (project.textContent || project.innerText);

                if (txtValue.toLowerCase().indexOf(filter) > -1) {
                    cards[i].style.display = "";
                } else {
                    cards[i].style.display = "none";
                }
            }
        }

        function openModal(mode, data = null) {
            document.getElementById('invoiceModal').classList.remove('hidden');
            const form = document.getElementById('invoiceForm');
            const title = document.getElementById('modalTitle');

            if (mode === 'create') {
                title.innerText = 'Create Invoice';
                form.reset();
                document.getElementById('invoiceId').value = '';
            } else if (mode === 'edit' && data) {
                title.innerText = 'Edit Invoice';
                document.getElementById('invoiceId').value = data.id;
                document.getElementById('project_id').value = data.project_id;
                document.getElementById('amount').value = data.amount;
                document.getElementById('status').value = data.status;
                document.getElementById('due_date').value = data.due_date ? data.due_date.substring(0, 10) : '';
            }
        }

        function closeModal() {
            document.getElementById('invoiceModal').classList.add('hidden');
        }

        async function submitInvoice(event) {
            event.preventDefault();
            const form = document.getElementById('invoiceForm');
            const formData = new FormData(form);
            const id = formData.get('id');
            const url = id ? `/invoices/${id}` : '/invoices';

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

        async function editInvoice(id) {
            try {
                const response = await fetch(`/invoices/${id}/edit`, {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();
                openModal('edit', data);
            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'Could not fetch invoice data', 'error');
            }
        }

        function deleteInvoice(id) {
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
                    fetch(`/invoices/${id}`, {
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
                        .catch(error => Swal.fire('Error', 'Could not delete invoice', 'error'));
                }
            })
        }
    </script>
</x-app-layout>
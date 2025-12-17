<x-app-layout>
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-2xl text-gray-800  leading-tight">
                {{ __('Invoices') }}
            </h2>
            <button onclick="openModal('create')"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded shadow transition transform hover:scale-105">
                <i class="fas fa-plus mr-2"></i> Create Invoice
            </button>
        </div>

        <div class="bg-white  overflow-hidden shadow-sm rounded-lg">
            <div class="p-6 overflow-x-auto">
                <table class="min-w-full leading-normal">
                    <thead>
                        <tr>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Invoice #</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Project</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Client</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Amount</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Status</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Due Date</th>
                            <th
                                class="px-5 py-3 border-b-2 border-gray-200  bg-gray-100  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($invoices as $invoice)
                            <tr class="hover:bg-gray-50 :bg-gray-700 transition">
                                <td
                                    class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap font-bold">
                                        {{ $invoice->invoice_number }}</p>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap">
                                        {{ $invoice->project->name }}</p>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap">
                                        {{ $invoice->project->client->name }}</p>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap font-mono">
                                        ${{ number_format($invoice->amount, 2) }}</p>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <span class="relative inline-block px-3 py-1 font-semibold leading-tight 
                                        {{ $invoice->status === 'Paid' ? 'text-green-900' : 'text-red-900' }}">
                                        <span aria-hidden class="absolute inset-0 opacity-50 rounded-full 
                                            {{ $invoice->status === 'Paid' ? 'bg-green-200' : 'bg-red-200' }}"></span>
                                        <span class="relative">{{ $invoice->status }}</span>
                                    </span>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <p class="text-gray-900  whitespace-no-wrap">
                                        {{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '-' }}</p>
                                </td>
                                <td
                                    class="px-5 py-5 border-b border-gray-200  bg-white  text-sm">
                                    <a href="{{ route('invoices.download', $invoice->id) }}"
                                        class="text-gray-500 hover:text-gray-700 mr-3 transition" title="Download PDF"><i
                                            class="fas fa-download"></i></a>
                                    <button onclick="editInvoice({{ $invoice->id }})"
                                        class="text-yellow-500 hover:text-yellow-700 mr-3 transition"><i
                                            class="fas fa-edit"></i></button>
                                    <button onclick="deleteInvoice({{ $invoice->id }})"
                                        class="text-red-500 hover:text-red-700 transition"><i
                                            class="fas fa-trash"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($invoices->isEmpty())
                    <div class="text-center py-4 text-gray-500">No invoices found.</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Invoice Modal -->
    <div id="invoiceModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white  rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="invoiceForm" onsubmit="submitInvoice(event)">
                    @csrf
                    <input type="hidden" id="invoiceId" name="id">
                    <div class="bg-white  px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 "
                                    id="modalTitle">Create Invoice</h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="project_id"
                                            class="block text-sm font-medium text-gray-700 ">Project</label>
                                        <select name="project_id" id="project_id"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                            <option value="">Select Project</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}">{{ $project->name }}
                                                    ({{ $project->client->name }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label for="amount"
                                            class="block text-sm font-medium text-gray-700 ">Amount
                                            ($)</label>
                                        <input type="number" step="0.01" name="amount" id="amount"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label for="status"
                                            class="block text-sm font-medium text-gray-700 ">Status</label>
                                        <select name="status" id="status"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                                            <option value="Pending">Pending</option>
                                            <option value="Paid">Paid</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label for="due_date"
                                            class="block text-sm font-medium text-gray-700 ">Due
                                            Date</label>
                                        <input type="date" name="due_date" id="due_date"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
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
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/invoices/${id}`, {
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
                        .catch(error => Swal.fire('Error', 'Could not delete invoice', 'error'));
                }
            })
        }
    </script>
</x-app-layout>
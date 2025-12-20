<x-app-layout>
    <div class="space-y-6">
        <h2 class="font-semibold text-2xl text-gray-800  leading-tight">
            {{ __('Finance Dashboard') }}
        </h2>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Total Revenue -->
            <div class="bg-white  overflow-hidden shadow-sm rounded-lg p-6 flex flex-col items-center justify-center">
                <div class="text-sm font-medium text-gray-500  uppercase tracking-widest">Total
                    Revenue</div>
                <div class="text-3xl font-bold text-green-600 mt-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
            </div>

            <!-- Total Expenses -->
            <div class="bg-white  overflow-hidden shadow-sm rounded-lg p-6 flex flex-col items-center justify-center">
                <div class="text-sm font-medium text-gray-500  uppercase tracking-widest">Total
                    Expenses</div>
                <div class="text-3xl font-bold text-red-600 mt-2">Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                </div>
            </div>

            <!-- Net Profit -->
            <div class="bg-white  overflow-hidden shadow-sm rounded-lg p-6 flex flex-col items-center justify-center">
                <div class="text-sm font-medium text-gray-500  uppercase tracking-widest">Net Profit
                </div>
                <div class="text-3xl font-bold {{ $profit >= 0 ? 'text-blue-600' : 'text-red-600' }} mt-2">
                    Rp {{ number_format($profit, 0, ',', '.') }}</div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Expenses Management -->
            <div class="bg-white  overflow-hidden shadow-sm rounded-lg">
                <div class="p-6 border-b border-gray-100  flex justify-between items-center">
                    <div class="font-bold text-lg text-gray-800 ">Expenses</div>
                    <button onclick="openModal('create')"
                        class="text-sm bg-red-100 text-red-600 px-3 py-1 rounded hover:bg-red-200 transition">
                        <i class="fas fa-plus mr-1"></i> Add Expense
                    </button>
                </div>
                <div class="p-6 overflow-y-auto max-h-96">
                    @if($expenses->count() > 0)
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200  bg-gray-50  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                        Date</th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200  bg-gray-50  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                        Description</th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200  bg-gray-50  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                        Amount</th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-200  bg-gray-50  text-left text-xs font-semibold text-gray-600  uppercase tracking-wider">
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($expenses as $expense)
                                    <tr>
                                        <td class="px-5 py-2 border-b border-gray-100  text-sm text-gray-700 ">
                                            {{ $expense->date ? $expense->date->format('M d, Y') : '-' }}
                                        </td>
                                        <td class="px-5 py-2 border-b border-gray-100  text-sm text-gray-700 ">
                                            {{ $expense->description }}
                                        </td>
                                        <td class="px-5 py-2 border-b border-gray-100  text-sm text-red-600 font-medium">
                                            Rp {{ number_format($expense->amount, 0, ',', '.') }}</td>
                                        <td class="px-5 py-2 border-b border-gray-100  text-sm text-right">
                                            <button onclick="deleteExpense({{ $expense->id }})"
                                                class="text-red-400 hover:text-red-600"><i class="fas fa-trash"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500  text-center">No expenses recorded.</p>
                    @endif
                </div>
            </div>

            <!-- Recent Income -->
            <div class="bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-800 tracking-tight">Recent Income (Paid Invoices)</h3>
                    <a href="{{ route('invoices.index') }}"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">View All</a>
                </div>
                <div class="p-2">
                    @if($invoices->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Date</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Project</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($invoices as $invoice)
                                        <tr class="hover:bg-gray-50/50 transition-colors group">
                                            <td class="px-6 py-4 text-sm text-gray-700">
                                                {{ $invoice->updated_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    <div
                                                        class="flex-shrink-0 w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs mr-3 border border-indigo-100">
                                                        {{ substr($invoice->project->name, 0, 2) }}
                                                    </div>
                                                    <div>
                                                        <span class="text-gray-900 font-medium block text-sm">
                                                            {{ $invoice->project->name }}
                                                        </span>
                                                        <span
                                                            class="text-xs text-gray-500">{{ $invoice->project->client->name ?? '' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-sm font-medium text-green-600">
                                                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-file-invoice-dollar text-gray-400"></i>
                            </div>
                            <h3 class="text-gray-900 font-medium text-sm">No paid invoices yet</h3>
                            <p class="text-gray-500 text-xs mt-1">Income will appear here once invoices are paid.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Modal -->
    <div id="expenseModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"
                onclick="closeModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white  rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <form id="expenseForm" onsubmit="submitExpense(event)">
                    @csrf
                    <div class="bg-white  px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                <h3 class="text-lg leading-6 font-medium text-gray-900 " id="modalTitle">Add Expense
                                </h3>
                                <div class="mt-4 space-y-4">
                                    <div>
                                        <label for="description"
                                            class="block text-sm font-medium text-gray-700 ">Description</label>
                                        <input type="text" name="description" id="description"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label for="amount" class="block text-sm font-medium text-gray-700 ">Amount
                                            (Rp)</label>
                                        <input type="number" step="0.01" name="amount" id="amount"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
                                    </div>
                                    <div>
                                        <label for="date" class="block text-sm font-medium text-gray-700 ">Date</label>
                                        <input type="date" name="date" id="date"
                                            class="mt-1 block w-full rounded-md border-gray-300   shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                                            required>
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
        function openModal() {
            document.getElementById('expenseModal').classList.remove('hidden');
            document.getElementById('expenseForm').reset();
            // Set today's date
            document.getElementById('date').valueAsDate = new Date();
        }

        function closeModal() {
            document.getElementById('expenseModal').classList.add('hidden');
        }

        async function submitExpense(event) {
            event.preventDefault();
            const form = document.getElementById('expenseForm');
            const formData = new FormData(form);
            const url = '/finance/expenses';

            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(url, {
                    method: 'POST',
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
                    location.reload();
                });
            } catch (error) {
                console.error('Error:', error);
                Swal.fire('Error', 'Something went wrong', 'error');
            }
        }

        function deleteExpense(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(`/finance/expenses/${id}`, {
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
                        .catch(error => Swal.fire('Error', 'Could not delete expense', 'error'));
                }
            })
        }
    </script>
</x-app-layout>
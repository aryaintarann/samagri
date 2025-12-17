<x-app-layout>
    <div class="space-y-6">
        <!-- Page Title -->
        <h2 class="font-semibold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Active Projects -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex items-center border border-gray-100">
                <div class="p-3 rounded-full bg-blue-50 text-blue-600 mr-4">
                    <i class="fas fa-project-diagram text-2xl"></i>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Active Projects</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $activeProjectsCount }}</div>
                </div>
            </div>

            <!-- Total Members -->
            <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex items-center border border-gray-100">
                <div class="p-3 rounded-full bg-green-50 text-green-600 mr-4">
                    <i class="fas fa-users text-2xl"></i>
                </div>
                <div>
                    <div class="text-sm font-medium text-gray-500">Total Members</div>
                    <div class="text-2xl font-bold text-gray-800">{{ $totalMembersCount }}</div>
                </div>
            </div>

            @if(auth()->user()->role === 'CEO')
                <!-- Total Revenue (CEO Only) -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex items-center border border-gray-100">
                    <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 mr-4">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Total Revenue</div>
                        <div class="text-2xl font-bold text-gray-800">
                            ${{ number_format($totalRevenue, 2) }}</div>
                    </div>
                </div>

                <!-- Pending Invoices (CEO Only) -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex items-center border border-gray-100">
                    <div class="p-3 rounded-full bg-red-50 text-red-600 mr-4">
                        <i class="fas fa-file-invoice text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Pending Invoices</div>
                        <div class="text-2xl font-bold text-gray-800">{{ $pendingInvoicesCount }}</div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Quick Actions & Active Projects -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Active Projects List -->
            <div class="lg:col-span-2 bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                <div class="p-6 border-b border-gray-100 font-bold text-lg text-gray-800">
                    Active Projects
                </div>
                <div class="p-6">
                    @if($activeProjects->count() > 0)
                        <table class="min-w-full leading-normal">
                            <thead>
                                <tr>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-100 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Project
                                    </th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-100 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Client
                                    </th>
                                    <th
                                        class="px-5 py-3 border-b-2 border-gray-100 bg-gray-50 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                        Status
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($activeProjects as $project)
                                    <tr>
                                        <td class="px-5 py-5 border-b border-gray-100 bg-white text-sm">
                                            <p class="text-gray-900 whitespace-no-wrap font-medium">{{ $project->name }}</p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-100 bg-white text-sm">
                                            <p class="text-gray-600 whitespace-no-wrap">{{ $project->client->name }}</p>
                                        </td>
                                        <td class="px-5 py-5 border-b border-gray-100 bg-white text-sm">
                                            <span
                                                class="relative inline-block px-3 py-1 font-semibold text-green-900 leading-tight">
                                                <span aria-hidden
                                                    class="absolute inset-0 bg-green-100 opacity-50 rounded-full"></span>
                                                <span class="relative text-green-700">Active</span>
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500">No active projects.</p>
                    @endif
                </div>
            </div>

            <!-- Right Column: Quick Actions & Activity Log -->
            <div class="space-y-6">
                <!-- Quick Actions -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100 font-bold text-lg text-gray-800">
                        Quick Actions
                    </div>
                    <div class="p-6 grid grid-cols-2 gap-4">
                        <a href="{{ route('projects.create') }}"
                            class="group block p-4 bg-gray-50 rounded-lg hover:bg-blue-50 transition border border-gray-200 text-center">
                            <i class="fas fa-plus text-blue-500 group-hover:text-blue-600 text-xl mb-2"></i>
                            <div class="text-sm font-medium text-gray-700">Add Project</div>
                        </a>
                        <a href="{{ route('invoices.create') }}"
                            class="group block p-4 bg-gray-50 rounded-lg hover:bg-green-50 transition border border-gray-200 text-center">
                            <i
                                class="fas fa-file-invoice-dollar text-green-500 group-hover:text-green-600 text-xl mb-2"></i>
                            <div class="text-sm font-medium text-gray-700">New Invoice</div>
                        </a>
                        <a href="{{ route('clients.create') }}"
                            class="group block p-4 bg-gray-50 rounded-lg hover:bg-purple-50 transition border border-gray-200 text-center">
                            <i class="fas fa-user-plus text-purple-500 group-hover:text-purple-600 text-xl mb-2"></i>
                            <div class="text-sm font-medium text-gray-700">Add Client</div>
                        </a>
                        <a href="{{ route('sops.create') }}"
                            class="group block p-4 bg-gray-50 rounded-lg hover:bg-yellow-50 transition border border-gray-200 text-center">
                            <i class="fas fa-book text-yellow-500 group-hover:text-yellow-600 text-xl mb-2"></i>
                            <div class="text-sm font-medium text-gray-700">Create SOP</div>
                        </a>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100 font-bold text-lg text-gray-800">
                        Recent Activity
                    </div>
                    <div class="p-6">
                        @if($recentLogs->count() > 0)
                            <ul class="space-y-3">
                                @foreach($recentLogs as $log)
                                    <li class="flex items-start">
                                        <div
                                            class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-600 mt-1">
                                            {{ substr($log->user->name, 0, 1) }}
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm text-gray-800">
                                                <span class="font-semibold">{{ $log->user->name }}</span> {{ $log->action }}
                                            </p>
                                            <p class="text-xs text-gray-500">{{ $log->created_at->diffForHumans() }}</p>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-gray-500 text-sm">No recent activity.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
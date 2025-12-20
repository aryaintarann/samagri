<x-app-layout>
    <div class="space-y-6">
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

            @if(auth()->user()->hasRole('CEO'))
                <!-- Total Revenue (CEO Only) -->
                <div class="bg-white overflow-hidden shadow-sm rounded-lg p-6 flex items-center border border-gray-100">
                    <div class="p-3 rounded-full bg-yellow-50 text-yellow-600 mr-4">
                        <i class="fas fa-dollar-sign text-2xl"></i>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Total Revenue</div>
                        <div class="text-2xl font-bold text-gray-800">
                            Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
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
            <div class="lg:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm">
                <div class="px-6 py-4 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-lg text-gray-800 tracking-tight">Active Projects</h3>
                    <a href="{{ route('projects.index') }}"
                        class="text-sm text-blue-600 hover:text-blue-700 font-medium hover:underline">View All</a>
                </div>
                <div class="p-2">
                    @if($activeProjects->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Project</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Client</th>
                                        <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach($activeProjects as $project)
                                                            <tr class="hover:bg-gray-50/50 transition-colors group">
                                                                <td class="px-6 py-4">
                                                                    <div class="flex items-center">
                                                                        <div
                                                                            class="flex-shrink-0 w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-xs mr-3 border border-indigo-100">
                                                                            {{ substr($project->name, 0, 2) }}
                                                                        </div>
                                                                        <div>
                                                                            <a href="{{ route('projects.show', $project->id) }}"
                                                                                class="text-gray-900 font-medium hover:text-blue-600 transition-colors block text-sm">
                                                                                {{ $project->name }}
                                                                            </a>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="px-6 py-4 text-sm text-gray-500">
                                                                    {{ $project->client->name }}
                                                                </td>
                                                                <td class="px-6 py-4 text-sm">
                                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border
                                                                                                                                                                    {{ $project->status === 'Completed' ? 'bg-green-50 text-green-700 border-green-100' :
                                        ($project->status === 'In Progress' ? 'bg-blue-50 text-blue-700 border-blue-100' :
                                            ($project->status === 'On Hold' ? 'bg-yellow-50 text-yellow-700 border-yellow-100' :
                                                'bg-gray-50 text-gray-700 border-gray-100')) }}">
                                                                        {{ $project->status }}
                                                                    </span>
                                                                </td>
                                                            </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-folder-open text-gray-400"></i>
                            </div>
                            <h3 class="text-gray-900 font-medium text-sm">No active projects</h3>
                            <p class="text-gray-500 text-xs mt-1">Start a new project to see it here.</p>
                        </div>
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
<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white text-gray-800 transition-transform duration-300 ease-in-out transform md:translate-x-0 md:static md:inset-0 shadow-xl border-r border-gray-200">
    <div class="flex justify-between items-center h-16 mt-4 px-6 md:justify-center">
        <img src="/logo.png" alt="Samagri Logo" class="h-10 w-auto">
        <!-- Close button for mobile -->
        <button @click="sidebarOpen = false" class="md:hidden text-gray-500 hover:text-gray-700">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>

    <!-- Profile removed as per request -->

    <nav class="flex-1 overflow-y-auto py-6 space-y-1">
        <a href="{{ route('dashboard') }}"
            class="flex items-center px-6 py-3.5 transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 hover:pl-7' }}">
            <i
                class="fas fa-home w-6 text-center mr-3 {{ request()->routeIs('dashboard') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }}"></i>
            <span class="font-medium">Dashboard</span>
        </a>

        <a href="{{ route('projects.index') }}"
            class="flex items-center px-6 py-3.5 transition-all duration-200 group {{ request()->routeIs('projects.*') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 hover:pl-7' }}">
            <i
                class="fas fa-project-diagram w-6 text-center mr-3 {{ request()->routeIs('projects.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }}"></i>
            <span class="font-medium">Projects</span>
        </a>

        <a href="{{ route('invoices.index') }}"
            class="flex items-center px-6 py-3.5 transition-all duration-200 group {{ request()->routeIs('invoices.*') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 hover:pl-7' }}">
            <i
                class="fas fa-file-invoice-dollar w-6 text-center mr-3 {{ request()->routeIs('invoices.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }}"></i>
            <span class="font-medium">Invoices</span>
        </a>

        @if(auth()->user()->hasRole('CEO'))
            <a href="{{ route('finance.index') }}"
                class="flex items-center px-6 py-3.5 transition-all duration-200 group {{ request()->routeIs('finance.*') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 hover:pl-7' }}">
                <i
                    class="fas fa-chart-line w-6 text-center mr-3 {{ request()->routeIs('finance.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }}"></i>
                <span class="font-medium">Finance</span>
            </a>
        @endif

        <a href="{{ route('sops.index') }}"
            class="flex items-center px-6 py-3.5 transition-all duration-200 group {{ request()->routeIs('sops.*') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 hover:pl-7' }}">
            <i
                class="fas fa-book w-6 text-center mr-3 {{ request()->routeIs('sops.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }}"></i>
            <span class="font-medium">Knowledge Base</span>
        </a>

        <a href="{{ route('clients.index') }}"
            class="flex items-center px-6 py-3.5 transition-all duration-200 group {{ request()->routeIs('clients.*') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 hover:pl-7' }}">
            <i
                class="fas fa-users w-6 text-center mr-3 {{ request()->routeIs('clients.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }}"></i>
            <span class="font-medium">Clients</span>
        </a>

        @if(auth()->user()->hasRole('CEO'))
            <a href="{{ route('users.index') }}"
                class="flex items-center px-6 py-3.5 transition-all duration-200 group {{ request()->routeIs('users.*') ? 'bg-blue-50 text-blue-700 border-r-4 border-blue-600' : 'text-gray-600 hover:bg-gray-50 hover:text-blue-600 hover:pl-7' }}">
                <i
                    class="fas fa-user-shield w-6 text-center mr-3 {{ request()->routeIs('users.*') ? 'text-blue-600' : 'text-gray-400 group-hover:text-blue-500' }}"></i>
                <span class="font-medium">User Management</span>
            </a>
        @endif
    </nav>

    <div class="p-4 border-t border-gray-100 bg-gray-50/50">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full bg-white hover:bg-red-50 text-gray-700 hover:text-red-600 border border-gray-200 text-sm py-2.5 rounded-lg shadow-sm transition-all duration-200 flex items-center justify-center group font-medium">
                <i class="fas fa-sign-out-alt mr-2 text-gray-400 group-hover:text-red-500"></i> Logout
            </button>
        </form>
    </div>
</aside>
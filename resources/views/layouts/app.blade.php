<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: true }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="NexaCRM - Premium CRM & Client Workspace SaaS">
    <title>{{ config('app.name', 'NexaCRM') }} @hasSection('title') - @yield('title') @endif</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>

    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>

    <!-- SortableJS (Drag & Drop) -->
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>

    <!-- Vite Assets -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        /* ====================== CUSTOM CSS ====================== */
        :root {
            --primary: #6366f1;
            --primary-dark: #4f46e5;
            --accent: #8b5cf6;
            --sidebar-width: 260px;
        }

        body { font-family: 'Inter', sans-serif; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            transition: width 0.3s ease, transform 0.3s ease;
            background: linear-gradient(180deg, #0f0f1a 0%, #1a1a2e 100%);
        }

        .sidebar-collapsed { width: 72px; }

        /* Glass card */
        .glass-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.08);
        }

        /* Gradient text */
        .gradient-text {
            background: linear-gradient(135deg, #6366f1, #8b5cf6, #06b6d4);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Stat card hover animation */
        .stat-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px rgba(99,102,241,0.15); }

        /* Kanban columns */
        .kanban-column { min-height: 200px; }
        .task-card { transition: transform 0.15s ease, box-shadow 0.15s ease; cursor: grab; }
        .task-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.15); }
        .task-card.sortable-ghost { opacity: 0.4; }

        /* Priority badges */
        .priority-low { @apply bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400; }
        .priority-medium { @apply bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400; }
        .priority-high { @apply bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400; }
        .priority-urgent { @apply bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400; }

        /* Status badges */
        .status-planning { @apply bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400; }
        .status-active { @apply bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400; }
        .status-review { @apply bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400; }
        .status-completed { @apply bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400; }
        .status-on_hold { @apply bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400; }

        /* Progress bar animation */
        .progress-bar { transition: width 1s ease-in-out; }

        /* Sidebar nav item */
        .nav-item {
            transition: all 0.2s ease;
            border-radius: 10px;
        }
        .nav-item:hover, .nav-item.active {
            background: rgba(99, 102, 241, 0.15);
            color: #818cf8;
        }
        .nav-item.active { border-left: 3px solid #6366f1; }

        /* Toast notifications */
        .toast {
            animation: slideIn 0.3s ease;
        }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        /* Online indicator */
        .online-dot {
            width: 10px; height: 10px;
            background: #10b981;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.2); }
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.3); border-radius: 10px; }

        /* Dark mode scrollbar */
        .dark ::-webkit-scrollbar-thumb { background: rgba(99,102,241,0.4); }

        /* Smooth transitions */
        * { transition: background-color 0.2s ease, border-color 0.2s ease; }
    </style>
</head>

<body class="bg-gray-50 dark:bg-[#0a0a14] text-gray-900 dark:text-gray-100 min-h-screen">

<!-- Toast Notifications -->
<div id="toast-container" class="fixed top-5 right-5 z-[9999] space-y-2 max-w-sm w-full pointer-events-none"></div>

<!-- Main Layout -->
<div class="flex h-screen overflow-hidden">

    <!-- ==================== SIDEBAR ==================== -->
    <aside class="sidebar flex-shrink-0 flex flex-col h-full overflow-y-auto z-30 shadow-2xl"
           :class="{ 'sidebar-collapsed': !sidebarOpen }">

        <!-- Logo -->
        <div class="px-4 py-5 flex items-center gap-3 border-b border-white/5">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background: linear-gradient(135deg, #6366f1, #8b5cf6)">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <span class="font-bold text-lg text-white tracking-tight" x-show="sidebarOpen">
                Nexa<span class="text-indigo-400">CRM</span>
            </span>
            <button @click="sidebarOpen = !sidebarOpen" class="ml-auto text-gray-400 hover:text-white transition" x-show="sidebarOpen">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
            </button>
        </div>

        <!-- Workspace Switcher -->
        @php
            $currentWorkspace = session('current_workspace');
            $workspaceObj = is_array($currentWorkspace) ? (object)$currentWorkspace : $currentWorkspace;
        @endphp
        @if($workspaceObj)
        <div class="px-3 py-3 border-b border-white/5">
            <div class="flex items-center gap-2 p-2 rounded-lg hover:bg-white/5 cursor-pointer transition" x-show="sidebarOpen">
                <div class="w-7 h-7 rounded-lg flex items-center justify-center text-xs font-bold text-white flex-shrink-0"
                     style="background: {{ $workspaceObj->color ?? '#6366f1' }}">
                    {{ substr($workspaceObj->name ?? 'W', 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-200 truncate">{{ $workspaceObj->name ?? 'Workspace' }}</p>
                    <p class="text-xs text-gray-500">{{ ucfirst($workspaceObj->plan ?? 'free') }} Plan</p>
                </div>
                <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l4-4 4 4m0 6l-4 4-4-4"/></svg>
            </div>
        </div>
        @endif

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-1">
            <p class="text-xs font-semibold text-gray-600 uppercase tracking-widest px-2 mb-2" x-show="sidebarOpen">Main</p>

            <a href="{{ route('dashboard') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-gray-400 {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                <span class="text-sm font-medium" x-show="sidebarOpen">Dashboard</span>
            </a>

            <a href="{{ route('projects.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-gray-400 {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span class="text-sm font-medium" x-show="sidebarOpen">Projects</span>
            </a>

            <a href="{{ route('calendar') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-gray-400 {{ request()->routeIs('calendar') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <span class="text-sm font-medium" x-show="sidebarOpen">Calendar</span>
            </a>

            <a href="{{ route('chat.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-gray-400 {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span class="text-sm font-medium" x-show="sidebarOpen">Chat</span>
            </a>

            <a href="{{ route('files.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-gray-400 {{ request()->routeIs('files.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/></svg>
                <span class="text-sm font-medium" x-show="sidebarOpen">Files</span>
            </a>

            <a href="{{ route('invoices.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-gray-400 {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="text-sm font-medium" x-show="sidebarOpen">Invoices</span>
            </a>

            <div class="pt-3 pb-1">
                <p class="text-xs font-semibold text-gray-600 uppercase tracking-widest px-2 mb-2" x-show="sidebarOpen">Workspaces</p>
            </div>

            <a href="{{ route('workspaces.index') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-gray-400">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                <span class="text-sm font-medium" x-show="sidebarOpen">All Workspaces</span>
            </a>

            <a href="{{ route('workspaces.create') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 text-gray-400">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span class="text-sm font-medium" x-show="sidebarOpen">New Workspace</span>
            </a>
        </nav>

        <!-- User Profile -->
        <div class="border-t border-white/5 p-3">
            <div class="flex items-center gap-3">
                <div class="relative flex-shrink-0">
                    <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}"
                         class="w-9 h-9 rounded-full object-cover">
                    <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-green-400 border-2 border-gray-900 rounded-full"></span>
                </div>
                <div class="flex-1 min-w-0" x-show="sidebarOpen">
                    <p class="text-sm font-semibold text-gray-200 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email }}</p>
                </div>
                <div x-show="sidebarOpen" class="flex items-center gap-1">
                    <!-- Dark Mode Toggle -->
                    <button @click="darkMode = !darkMode" class="p-1.5 rounded-lg text-gray-400 hover:text-white hover:bg-white/10 transition">
                        <svg x-show="!darkMode" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                        <svg x-show="darkMode" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </button>

                    <!-- Logout -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg text-gray-400 hover:text-red-400 hover:bg-red-400/10 transition">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <!-- ==================== MAIN CONTENT ==================== -->
    <div class="flex-1 flex flex-col overflow-hidden">

        <!-- Top Bar -->
        <header class="h-16 bg-white dark:bg-[#0f0f1a] border-b border-gray-100 dark:border-white/5 flex items-center px-6 gap-4 flex-shrink-0 shadow-sm">

            <!-- Mobile sidebar toggle -->
            <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>

            <!-- Page title / breadcrumb -->
            <div class="flex-1">
                @yield('breadcrumb')
            </div>

            <!-- Search -->
            <div class="hidden md:flex items-center gap-2 bg-gray-100 dark:bg-white/5 rounded-xl px-4 py-2 w-64">
                <svg class="w-4 h-4 text-gray-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <input type="text" id="global-search" placeholder="Search..." class="bg-transparent text-sm text-gray-600 dark:text-gray-300 placeholder-gray-400 outline-none flex-1">
                <kbd class="text-xs text-gray-400 bg-gray-200 dark:bg-white/10 px-1.5 py-0.5 rounded">⌘K</kbd>
            </div>

            <!-- Notifications Bell -->
            <button class="relative p-2 rounded-xl text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-white/5 transition">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full"></span>
            </button>

            <!-- User avatar -->
            <a href="{{ route('profile.edit') }}" class="flex items-center gap-2">
                <img src="{{ auth()->user()->avatar_url }}" alt="{{ auth()->user()->name }}" class="w-8 h-8 rounded-full">
            </a>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto bg-gray-50 dark:bg-[#0a0a14]">
            <!-- Flash Messages -->
            @if(session('success'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mx-6 mt-4 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl text-green-700 dark:text-green-400 flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                {{ session('success') }}
                <button @click="show = false" class="ml-auto text-green-500">✕</button>
            </div>
            @endif

            @if(session('error'))
            <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                 class="mx-6 mt-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-400 flex items-center gap-3">
                <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<!-- Toast JS -->
<script>
function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    const colors = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        info: 'bg-blue-500',
        warning: 'bg-yellow-500',
    };
    toast.className = `toast pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-xl text-white text-sm shadow-xl ${colors[type] || colors.success}`;
    toast.innerHTML = `<span>${message}</span><button onclick="this.parentElement.remove()" class="ml-2 opacity-70 hover:opacity-100">✕</button>`;
    container.appendChild(toast);
    setTimeout(() => toast.remove(), 4000);
}

// Setup CSRF for fetch
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
</script>

@stack('scripts')
</body>
</html>

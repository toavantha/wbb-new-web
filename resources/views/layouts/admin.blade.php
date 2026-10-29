<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Admin Dashboard') | Welfare Benefits Board - Sri Lanka</title>

    <!-- Favicon -->
    <link rel="icon" type="image/webp" href="{{ asset('images/welfare-benefits-board-vacancies.webp') }}">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Tailwind CSS -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-100 text-slate-800 font-sans antialiased flex h-screen overflow-hidden">

    <!-- Sidebar Navigation -->
    <aside id="admin-sidebar" class="w-64 bg-wbb-900 text-slate-300 flex flex-col shrink-0 transition-all duration-300 z-40 fixed inset-y-0 left-0 lg:static lg:translate-x-0 -translate-x-full">
        <!-- Brand Header -->
        <div class="h-20 flex items-center gap-3 px-6 border-b border-slate-800 bg-wbb-900/90">
            <img src="http://wbb.gov.lk/assets/img/gov.png" alt="Emblem" class="h-9 w-auto">
            <div>
                <div class="font-bold text-white text-base leading-tight">WBB CMS</div>
                <div class="text-[10px] text-amber-400 font-semibold tracking-wider uppercase">Website Admin Panel</div>
            </div>
        </div>

        <!-- Navigation Links -->
        <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1 text-xs font-semibold">
            <div class="text-[10px] font-bold text-slate-400 px-3 py-1 uppercase tracking-wider">Site Administration</div>

            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.dashboard') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                <span>Dashboard Overview</span>
            </a>

            <a href="{{ route('admin.activity.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.activity.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="history" class="w-4 h-4"></i>
                <span>CMS Activity</span>
            </a>

            <div class="pt-4 text-[10px] font-bold text-slate-400 px-3 py-1 uppercase tracking-wider">Content Modules</div>

            <a href="{{ route('admin.news.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.news.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="newspaper" class="w-4 h-4"></i>
                <span>News & Press Releases</span>
            </a>

            <a href="{{ route('admin.publications.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.publications.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="file-text" class="w-4 h-4"></i>
                <span>Publications & Circulars</span>
            </a>

            <a href="{{ route('admin.downloads.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.downloads.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Forms & Handbooks</span>
            </a>

            <a href="{{ route('admin.welfare-schemes.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.welfare-schemes.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="hand-heart" class="w-4 h-4"></i>
                <span>Welfare Schemes</span>
            </a>

            <a href="{{ route('admin.careers.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.careers.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="briefcase" class="w-4 h-4"></i>
                <span>Vacancies & Careers</span>
            </a>

            <a href="{{ route('admin.procurements.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.procurements.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="clipboard-list" class="w-4 h-4"></i>
                <span>Procurements & Tenders</span>
            </a>

            <a href="{{ route('admin.employees.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.employees.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                <i data-lucide="users" class="w-4 h-4"></i>
                <span>Employee Directory</span>
            </a>

            @can('manage-cms-users')
                <a href="{{ route('admin.board-members.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.board-members.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="contact-round" class="w-4 h-4"></i>
                    <span>Board of Directors</span>
                </a>
                <a href="{{ route('admin.government-affiliations.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.government-affiliations.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="landmark" class="w-4 h-4"></i>
                    <span>Government Affiliation</span>
                </a>
                <a href="{{ route('admin.contact-information.edit') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.contact-information.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="map-pin" class="w-4 h-4"></i>
                    <span>Contact Information</span>
                </a>
                <a href="{{ route('admin.users.current') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.users.current') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    <span>Current CMS Users</span>
                </a>
                <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.users.index') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i data-lucide="user-check" class="w-4 h-4"></i>
                    <span>User Registrations</span>
                </a>
            @endcan

            <a href="{{ route('admin.contacts.index') }}" class="flex items-center justify-between px-3 py-2.5 rounded-lg transition {{ request()->routeIs('admin.contacts.*') ? 'bg-amber-500 text-slate-950 font-bold shadow-xs' : 'hover:bg-slate-800 hover:text-white' }}">
                <div class="flex items-center gap-3">
                    <i data-lucide="mail" class="w-4 h-4"></i>
                    <span>Citizen Inquiries</span>
                </div>
                @php
                    $newMsgCount = \App\Models\ContactMessage::where('status', 'New')->count();
                @endphp
                @if($newMsgCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ request()->routeIs('admin.contacts.*') ? 'bg-slate-950 text-white' : 'bg-blue-500 text-white' }}">
                        {{ $newMsgCount }}
                    </span>
                @endif
            </a>

            <div class="pt-4 text-[10px] font-bold text-slate-400 px-3 py-1 uppercase tracking-wider">External Systems</div>

            <a href="https://iwms.wbb.gov.lk" target="_blank" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-amber-400 transition">
                <div class="flex items-center gap-3">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    <span>IWMS System</span>
                </div>
                <span class="text-[10px] text-slate-400">External</span>
            </a>

            <a href="https://eservices.wbb.gov.lk" target="_blank" class="flex items-center justify-between px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-amber-400 transition">
                <div class="flex items-center gap-3">
                    <i data-lucide="external-link" class="w-4 h-4"></i>
                    <span>eServices System</span>
                </div>
                <span class="text-[10px] text-slate-400">External</span>
            </a>

            <a href="{{ route('home') }}" target="_blank" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-400 hover:bg-slate-800 hover:text-white transition">
                <i data-lucide="globe" class="w-4 h-4"></i>
                <span>View Public Site</span>
            </a>
        </nav>

        <!-- Current User Card & Logout -->
        <div class="p-4 border-t border-slate-800 bg-wbb-900/60">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-full bg-amber-500 text-slate-950 font-bold flex items-center justify-center text-sm shrink-0">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                </div>
                <div class="overflow-hidden">
                    <div class="text-xs font-bold text-white truncate">{{ Auth::user()->name ?? 'Administrator' }}</div>
                    <div class="text-[10px] text-amber-400 font-medium capitalize">{{ Auth::user()->role ?? 'Admin' }}</div>
                </div>
            </div>
            <a href="{{ route('admin.password.edit') }}" class="mb-2 flex w-full items-center justify-center gap-2 rounded-lg bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-300 transition hover:bg-slate-700 hover:text-white">
                <i data-lucide="key-round" class="h-3.5 w-3.5"></i>
                <span>Change Password</span>
            </a>
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 bg-slate-800 hover:bg-rose-900/60 hover:text-rose-200 text-slate-300 rounded-lg text-xs font-semibold transition">
                    <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
                    <span>Log Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
        <!-- Top Header Bar -->
        <header class="h-20 bg-white border-b border-slate-200 px-4 sm:px-6 lg:px-8 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-4">
                <button id="sidebar-toggle" class="lg:hidden p-2 rounded-lg text-slate-600 hover:bg-slate-100">
                    <i data-lucide="menu" class="w-5 h-5"></i>
                </button>
                <div>
                    <h1 class="text-base sm:text-lg font-bold text-slate-900">@yield('page_title', 'Administrative Dashboard')</h1>
                    <div class="text-xs text-slate-500">Welfare Benefits Board of Sri Lanka</div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('home') }}" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition">
                    <i data-lucide="globe" class="w-3.5 h-3.5 text-slate-500"></i>
                    <span>Public Site</span>
                </a>
                <div class="text-xs text-slate-500 bg-slate-100 px-3 py-1.5 rounded-lg font-mono">
                    {{ date('Y-m-d') }}
                </div>
            </div>
        </header>

        <!-- Main Workspace (Scrollable) -->
        <main class="flex-1 overflow-y-auto p-4 sm:p-6 lg:p-8">
            <!-- Flash Notifications -->
            @if(session('success'))
                <div class="mb-6 bg-emerald-50 border border-emerald-300 text-emerald-800 px-4 py-3 rounded-xl flex items-start gap-3 shadow-xs">
                    <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5"></i>
                    <div class="text-xs sm:text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-xl flex items-start gap-3 shadow-xs">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 shrink-0 mt-0.5"></i>
                    <div class="text-xs sm:text-sm font-medium">{{ session('error') }}</div>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-6 bg-rose-50 border border-rose-300 text-rose-800 px-4 py-3 rounded-xl shadow-xs">
                    <div class="font-bold text-xs uppercase tracking-wider mb-1 flex items-center gap-1.5">
                        <i data-lucide="alert-circle" class="w-4 h-4 text-rose-600"></i>
                        Please resolve the following input errors:
                    </div>
                    <ul class="list-disc list-inside text-xs space-y-0.5">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>

<!DOCTYPE html>
<html lang="fr" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'ALBOPTIC CRM — Optique & Lunetterie' }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-50 selection:bg-[#E82229] selection:text-white" x-data="{ mobileOpen: false }">
    <div class="min-h-full flex">
        <!-- Desktop Sidebar: Navy #172B4D -->
        <aside class="hidden lg:flex lg:flex-col lg:w-72 lg:fixed lg:inset-y-0 bg-[#172B4D] text-slate-200 border-r border-[#0F1D35] shadow-2xl z-30">
            <!-- Sidebar Header -->
            <div class="flex items-center gap-3 px-6 h-20 border-b border-white/10 bg-[#0F1D35]/50">
                <div class="flex items-center justify-center w-11 h-11 rounded-xl bg-[#E82229] text-white shadow-lg shadow-[#E82229]/30">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                </div>
                <div>
                    <span class="text-xl font-black text-white tracking-wide block">ALBOPTIC</span>
                    <span class="text-xs font-medium text-slate-300 block">CRM Optique • Marrakech</span>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-6 space-y-1.5 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('dashboard') ? 'bg-[#E82229] text-white shadow-lg shadow-[#E82229]/25' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white hover:shadow-md' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                    </svg>
                    <span>Tableau de bord</span>
                </a>

                <a href="{{ route('clients.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('clients.*') ? 'bg-[#E82229] text-white shadow-lg shadow-[#E82229]/25' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white hover:shadow-md' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Clients</span>
                </a>

                <a href="{{ route('prescriptions.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('prescriptions.*') ? 'bg-[#E82229] text-white shadow-lg shadow-[#E82229]/25' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white hover:shadow-md' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Ordonnances</span>
                </a>

                <a href="{{ route('orders.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('orders.*') ? 'bg-[#E82229] text-white shadow-lg shadow-[#E82229]/25' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white hover:shadow-md' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                    <span>Commandes</span>
                </a>

                <a href="{{ route('payments.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('payments.*') ? 'bg-[#E82229] text-white shadow-lg shadow-[#E82229]/25' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white hover:shadow-md' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    <span>Paiements</span>
                </a>

                <a href="{{ route('documents.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('documents.*') ? 'bg-[#E82229] text-white shadow-lg shadow-[#E82229]/25' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white hover:shadow-md' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Factures & Bons</span>
                </a>

                <a href="{{ route('reminders.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('reminders.*') ? 'bg-[#E82229] text-white shadow-lg shadow-[#E82229]/25' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white hover:shadow-md' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                    <span>Rappels & Tâches</span>
                </a>

                <a href="{{ route('analytics.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('analytics.*') ? 'bg-[#E82229] text-white shadow-lg shadow-[#E82229]/25' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white hover:shadow-md' }}">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <span>Analytiques</span>
                </a>

                @can('manage-settings')
                    <div class="pt-4 mt-4 border-t border-white/10">
                        <span class="px-4 text-[11px] font-semibold uppercase tracking-wider text-slate-400 block mb-2">Administration</span>
                        <a href="{{ route('settings.index') }}" class="flex items-center gap-3.5 px-4 py-3 rounded-xl text-sm font-semibold transition {{ request()->routeIs('settings.*') ? 'bg-[#E82229] text-white shadow-lg shadow-[#E82229]/25' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white hover:shadow-md' }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>Paramètres du magasin</span>
                        </a>
                    </div>
                @endcan
            </nav>

            <!-- Store & User Footer -->
            <div class="p-4 border-t border-white/10 bg-[#0F1D35]/60">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-[#203A66] flex items-center justify-center font-bold text-sm text-white border border-white/15">
                            {{ substr(auth()->user()->name ?? 'U', 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-300 truncate">{{ auth()->user()->role->label() }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Se déconnecter" class="p-2 text-slate-400 hover:text-[#E82229] hover:bg-white/10 rounded-lg transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Mobile Drawer Backdrop -->
        <div
            x-show="mobileOpen"
            x-cloak
            @click="mobileOpen = false"
            class="fixed inset-0 z-40 bg-slate-900/80 backdrop-blur-sm lg:hidden"
        ></div>

        <!-- Mobile Drawer Sidebar -->
        <div
            x-show="mobileOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-50 w-72 bg-[#172B4D] text-slate-200 border-r border-[#0F1D35] flex flex-col lg:hidden"
        >
            <div class="flex items-center justify-between px-6 h-20 border-b border-white/10 bg-[#0F1D35]">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-[#E82229] flex items-center justify-center text-white font-bold">A</div>
                    <span class="text-xl font-bold text-white">ALBOPTIC</span>
                </div>
                <button @click="mobileOpen = false" class="text-slate-400 hover:text-white p-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-xl font-medium {{ request()->routeIs('dashboard') ? 'bg-[#E82229] text-white' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white' }}">Tableau de bord</a>
                <a href="{{ route('clients.index') }}" class="block px-4 py-3 rounded-xl font-medium {{ request()->routeIs('clients.*') ? 'bg-[#E82229] text-white' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white' }}">Clients</a>
                <a href="{{ route('prescriptions.index') }}" class="block px-4 py-3 rounded-xl font-medium {{ request()->routeIs('prescriptions.*') ? 'bg-[#E82229] text-white' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white' }}">Ordonnances</a>
                <a href="{{ route('orders.index') }}" class="block px-4 py-3 rounded-xl font-medium {{ request()->routeIs('orders.*') ? 'bg-[#E82229] text-white' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white' }}">Commandes</a>
                <a href="{{ route('payments.index') }}" class="block px-4 py-3 rounded-xl font-medium {{ request()->routeIs('payments.*') ? 'bg-[#E82229] text-white' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white' }}">Paiements</a>
                <a href="{{ route('documents.index') }}" class="block px-4 py-3 rounded-xl font-medium {{ request()->routeIs('documents.*') ? 'bg-[#E82229] text-white' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white' }}">Factures & Documents</a>
                <a href="{{ route('reminders.index') }}" class="block px-4 py-3 rounded-xl font-medium {{ request()->routeIs('reminders.*') ? 'bg-[#E82229] text-white' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white' }}">Rappels & Tâches</a>
                <a href="{{ route('analytics.index') }}" class="block px-4 py-3 rounded-xl font-medium {{ request()->routeIs('analytics.*') ? 'bg-[#E82229] text-white' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white' }}">Analytiques</a>
                @can('manage-settings')
                    <a href="{{ route('settings.index') }}" class="block px-4 py-3 rounded-xl font-medium {{ request()->routeIs('settings.*') ? 'bg-[#E82229] text-white' : 'text-slate-300 hover:text-[#172B4D] hover:bg-white' }}">Paramètres</a>
                @endcan
            </nav>
            <div class="p-4 border-t border-white/10 bg-[#0F1D35]">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-white/10 hover:bg-[#E82229] text-white text-sm font-semibold transition text-center">
                        Se déconnecter
                    </button>
                </form>
            </div>
        </div>

        <!-- Main Layout Area: Clean White & Light Slate Workspace -->
        <div class="flex-1 lg:pl-72 flex flex-col min-w-0 bg-slate-50">
            <!-- Top Navigation Bar -->
            <header class="h-20 bg-white border-b border-slate-200/90 sticky top-0 z-20 flex items-center justify-between px-4 sm:px-8 shadow-xs">
                <div class="flex items-center gap-4">
                    <button @click="mobileOpen = true" class="lg:hidden p-2 text-slate-600 hover:text-slate-900 rounded-lg focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <div>
                        <h1 class="text-lg sm:text-xl font-bold text-[#172B4D]">{{ $header ?? 'Magasin ALBOPTIC' }}</h1>
                        <p class="text-xs text-slate-500 hidden sm:block">Marrakech, Guéliz • Point de vente optique</p>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="hidden md:flex items-center gap-2.5 bg-slate-100/90 border border-slate-200 rounded-full py-1.5 px-4 text-xs text-slate-700">
                        <span class="w-2 h-2 rounded-full bg-[#0F9D91]"></span>
                        <span>Session : <strong class="text-[#172B4D] font-semibold">{{ auth()->user()->name }}</strong></span>
                        <span class="bg-[#172B4D]/10 text-[#172B4D] border border-[#172B4D]/20 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase">{{ auth()->user()->role->label() }}</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300/80 transition">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span>Déconnexion</span>
                        </button>
                    </form>
                </div>
            </header>

            <!-- Flash Messages -->
            @if (session('success'))
                <div class="mx-4 sm:mx-8 mt-6 p-4 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-[#0F9D91] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="font-medium">{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if (session('error'))
                <div class="mx-4 sm:mx-8 mt-6 p-4 rounded-xl bg-red-50 border border-red-200 text-[#E82229] text-sm flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-[#E82229] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                        <span class="font-medium">{{ session('error') }}</span>
                    </div>
                </div>
            @endif

            <!-- Main Content Area: Comfortable Light Theme for Long Work Sessions -->
            <main class="flex-1 p-4 sm:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>

    @livewireScripts
</body>
</html>

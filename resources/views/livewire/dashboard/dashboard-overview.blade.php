<div>
    <!-- Page Header & Quick Actions -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-[#172B4D] tracking-tight">Tableau de bord de l'activité</h2>
            <p class="text-sm text-slate-500 mt-1">Aperçu en temps réel des ventes, encaissements et suivi du magasin.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('orders.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-[#E82229] hover:bg-[#D0181F] text-white font-semibold text-sm shadow-md shadow-[#E82229]/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Nouvelle Commande</span>
            </a>
            <a href="{{ route('clients.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-[#172B4D] border border-slate-300 font-semibold text-sm shadow-xs transition">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                </svg>
                <span>Nouveau Client</span>
            </a>
        </div>
    </div>

    <!-- 5 Core KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-8">
        <!-- Card 1: Today's Sales (Navy Accent) -->
        <div class="bg-white border border-slate-200/90 p-5 rounded-2xl shadow-xs relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Ventes du jour</span>
                <div class="w-9 h-9 rounded-xl bg-[#172B4D]/10 text-[#172B4D] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-black text-[#172B4D] tracking-tight">
                    {{ number_format($todaySales, 2, ',', ' ') }} <span class="text-xs font-bold text-slate-400">{{ $storeSetting->currency }}</span>
                </p>
                <p class="text-xs text-slate-500 mt-1">Chiffre d'affaires engagé aujourd'hui</p>
            </div>
        </div>

        <!-- Card 2: Today's Payments (Teal #0F9D91 Accent) -->
        <div class="bg-white border border-slate-200/90 p-5 rounded-2xl shadow-xs relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Paiements reçus</span>
                <div class="w-9 h-9 rounded-xl bg-[#0F9D91]/10 text-[#0F9D91] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-black text-[#0F9D91] tracking-tight">
                    {{ number_format($todayPayments, 2, ',', ' ') }} <span class="text-xs font-bold text-[#0F9D91]/80">{{ $storeSetting->currency }}</span>
                </p>
                <p class="text-xs text-slate-500 mt-1">Avances et soldes encaissés</p>
            </div>
        </div>

        <!-- Card 3: Outstanding Debt (Amber / Red Accent) -->
        <div class="bg-white border border-slate-200/90 p-5 rounded-2xl shadow-xs relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Reste à payer</span>
                <div class="w-9 h-9 rounded-xl bg-amber-500/10 text-amber-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-black text-amber-700 tracking-tight">
                    {{ number_format($outstandingPayments, 2, ',', ' ') }} <span class="text-xs font-bold text-amber-700/80">{{ $storeSetting->currency }}</span>
                </p>
                <p class="text-xs text-slate-500 mt-1">Créances en cours de règlement</p>
            </div>
        </div>

        <!-- Card 4: Today's Orders Count (Blue Accent) -->
        <div class="bg-white border border-slate-200/90 p-5 rounded-2xl shadow-xs relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Commandes du jour</span>
                <div class="w-9 h-9 rounded-xl bg-blue-500/10 text-blue-700 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-black text-[#172B4D] tracking-tight">
                    {{ $todayOrdersCount }} <span class="text-xs font-bold text-slate-400">dossiers</span>
                </p>
                <p class="text-xs text-slate-500 mt-1">Créées aujourd'hui</p>
            </div>
        </div>

        <!-- Card 5: Total Clients (Navy/Slate Accent) -->
        <div class="bg-white border border-slate-200/90 p-5 rounded-2xl shadow-xs relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Total Clients</span>
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-[#172B4D] flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <p class="text-2xl font-black text-[#172B4D] tracking-tight">
                    {{ $totalClientsCount }} <span class="text-xs font-bold text-slate-400">fiches</span>
                </p>
                <p class="text-xs text-slate-500 mt-1">Patients enregistrés</p>
            </div>
        </div>
    </div>

    <!-- Main Grid: Recent Orders & Side Widgets -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Orders Table (White Card + Comfortable Border) -->
        <div class="lg:col-span-2 bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                <div class="flex items-center gap-2.5">
                    <div class="w-3 h-3 rounded-full bg-[#172B4D]"></div>
                    <h3 class="text-lg font-bold text-[#172B4D]">Dernières commandes enregistrées</h3>
                </div>
                <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-[#E82229] hover:underline transition">Voir tout &rarr;</a>
            </div>

            <div class="overflow-x-auto mt-4">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3 px-3">N° Commande</th>
                            <th class="py-3 px-3">Client</th>
                            <th class="py-3 px-3">Date</th>
                            <th class="py-3 px-3">Total TTC</th>
                            <th class="py-3 px-3">État</th>
                            <th class="py-3 px-3 text-right">Paiement</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($recentOrders as $order)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3 font-mono font-semibold text-[#172B4D]">
                                    {{ $order->order_number }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="font-bold text-[#172B4D]">{{ $order->client->full_name }}</div>
                                    <div class="text-xs text-slate-500">{{ $order->client->phone }}</div>
                                </td>
                                <td class="py-3.5 px-3 text-slate-600 text-xs">
                                    {{ $order->order_date->format('d/m/Y') }}
                                </td>
                                <td class="py-3.5 px-3 font-bold text-[#172B4D]">
                                    {{ number_format((float) $order->total_amount, 2, ',', ' ') }} {{ $storeSetting->currency }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $order->status->badgeClasses() }}">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $order->payment_status->badgeClasses() }}">
                                        {{ $order->payment_status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    Aucune commande enregistrée pour le moment.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Side Column: Reminders & Outstanding Balances -->
        <div class="space-y-8">
            <!-- Upcoming Reminders Widget -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <h3 class="text-base font-bold text-[#172B4D]">Rappels à traiter</h3>
                    </div>
                    <a href="{{ route('reminders.index') }}" class="text-xs font-semibold text-amber-700 hover:underline transition">Gérer &rarr;</a>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($upcomingReminders as $reminder)
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 hover:border-slate-300 transition">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-amber-800">{{ $reminder->reminder_type->label() }}</span>
                                <span class="text-[11px] font-medium text-slate-500">{{ $reminder->reminder_date->format('d/m/Y') }}</span>
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <p class="text-sm font-bold text-[#172B4D]">{{ $reminder->client->full_name }}</p>
                                <a href="tel:{{ $reminder->client->phone }}" class="text-xs font-semibold text-[#0F9D91] hover:underline">{{ $reminder->client->phone }}</a>
                            </div>
                            @if ($reminder->notes)
                                <p class="text-xs text-slate-500 mt-1 italic">{{ $reminder->notes }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">Aucun rappel urgent en attente.</p>
                    @endforelse
                </div>
            </div>

            <!-- Outstanding Balances Widget -->
            <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-[#E82229]"></span>
                        <h3 class="text-base font-bold text-[#172B4D]">Soldes prioritaires à régler</h3>
                    </div>
                    <a href="{{ route('orders.index') }}" class="text-xs font-semibold text-[#E82229] hover:underline transition">Détails &rarr;</a>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse ($outstandingOrders as $order)
                        <div class="p-3.5 rounded-xl bg-red-50/50 border border-red-100 flex items-center justify-between">
                            <div>
                                <p class="text-sm font-bold text-[#172B4D]">{{ $order->client->full_name }}</p>
                                <p class="text-xs text-slate-500 font-mono">{{ $order->order_number }} • {{ $order->client->phone }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-[#E82229]">{{ number_format((float) $order->remaining_amount, 2, ',', ' ') }} {{ $storeSetting->currency }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">sur {{ number_format((float) $order->total_amount, 2, ',', ' ') }}</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 py-4 text-center">Toutes les commandes sont réglées à 100%.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

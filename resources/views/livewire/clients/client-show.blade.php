<div class="space-y-8">
    <!-- Back to List & Breadcrumb -->
    <div class="flex items-center justify-between">
        <a href="{{ route('clients.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 hover:text-[#172B4D] transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Retour au Répertoire Clients</span>
        </a>
        <span class="text-xs text-slate-400 font-mono">Dossier N° #CL-{{ sprintf('%05d', $client->id) }}</span>
    </div>

    <!-- Patient Header Card -->
    <div class="bg-white border border-slate-200/90 rounded-3xl p-6 sm:p-8 shadow-xs">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <!-- Left Info -->
            <div class="flex items-start gap-4 sm:gap-6">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-[#172B4D] text-white flex items-center justify-center font-black text-2xl sm:text-3xl shadow-lg shadow-[#172B4D]/20 flex-shrink-0">
                    {{ strtoupper(substr($client->first_name, 0, 1) . substr($client->last_name, 0, 1)) }}
                </div>
                <div>
                    <div class="flex flex-wrap items-center gap-3">
                        <h2 class="text-2xl sm:text-3xl font-black text-[#172B4D] tracking-tight">{{ $client->full_name }}</h2>
                        @if ($client->gender)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                {{ $client->gender === 'male' ? 'Homme' : ($client->gender === 'female' ? 'Femme' : 'Enfant') }}
                            </span>
                        @endif
                        @if ($client->age)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                {{ $client->age }} ans
                            </span>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-y-2 gap-x-6 mt-3 text-sm text-slate-600">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            <a href="tel:{{ $client->phone }}" class="font-bold text-[#172B4D] hover:text-[#0F9D91] transition">{{ $client->phone }}</a>
                        </div>
                        @if ($client->email)
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span>{{ $client->email }}</span>
                            </div>
                        @endif
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ $client->address ?: 'Marrakech' }}, {{ $client->city ?? 'Marrakech' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Financial KPIs Header & Actions -->
            <div class="flex flex-wrap items-center gap-3 border-t lg:border-t-0 lg:border-l border-slate-100 pt-4 lg:pt-0 lg:pl-8">
                <!-- Lifetime spend -->
                <div class="p-3.5 bg-slate-50 border border-slate-200/80 rounded-2xl min-w-[125px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Total Achats</span>
                    <span class="text-base font-black text-[#172B4D]">{{ number_format($totalSpend, 2, ',', ' ') }} <small class="text-[11px] font-bold text-slate-400">{{ $storeSetting->currency }}</small></span>
                </div>

                <!-- Total Paid (Teal #0F9D91) -->
                <div class="p-3.5 bg-teal-50 border border-teal-200/80 rounded-2xl min-w-[125px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-[#0F9D91] block">Total Réglé</span>
                    <span class="text-base font-black text-[#0F9D91]">{{ number_format($totalPaid, 2, ',', ' ') }} <small class="text-[11px] font-bold text-[#0F9D91]/70">{{ $storeSetting->currency }}</small></span>
                </div>

                <!-- Outstanding debt (Red #E82229 or Slate) -->
                <div class="p-3.5 {{ $totalDebt > 0 ? 'bg-red-50 border-red-200' : 'bg-slate-50 border-slate-200/80' }} border rounded-2xl min-w-[125px]">
                    <span class="text-[10px] font-bold uppercase tracking-wider {{ $totalDebt > 0 ? 'text-[#E82229]' : 'text-slate-400' }} block">Solde Restant</span>
                    <span class="text-base font-black {{ $totalDebt > 0 ? 'text-[#E82229]' : 'text-slate-700' }}">
                        {{ number_format($totalDebt, 2, ',', ' ') }} <small class="text-[11px] font-bold {{ $totalDebt > 0 ? 'text-[#E82229]/70' : 'text-slate-400' }}">{{ $storeSetting->currency }}</small>
                    </span>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-2 w-full sm:w-auto mt-2 sm:mt-0">
                    <button wire:click="openCreatePrescriptionModal" class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-[#E82229] hover:bg-[#c91a21] text-white font-bold text-xs shadow-md shadow-[#E82229]/20 transition flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>+ Ordonnance</span>
                    </button>
                    <button wire:click="openEditModal" class="w-full sm:w-auto px-3.5 py-2.5 rounded-xl border border-slate-300 hover:bg-slate-100 text-slate-700 font-bold text-xs transition text-center shadow-xs">
                        Modifier la fiche
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabbed Navigation -->
    <div class="border-b border-slate-200">
        <nav class="flex space-x-2 sm:space-x-8 overflow-x-auto">
            <button
                wire:click="$set('activeTab', 'overview')"
                class="py-3 px-2 border-b-2 font-bold text-sm whitespace-nowrap transition {{ $activeTab === 'overview' ? 'border-[#E82229] text-[#E82229]' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
            >
                Vue d'ensemble
            </button>
            <button
                wire:click="$set('activeTab', 'prescriptions')"
                class="py-3 px-2 border-b-2 font-bold text-sm whitespace-nowrap transition {{ $activeTab === 'prescriptions' ? 'border-[#E82229] text-[#E82229]' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
            >
                Ordonnances ({{ $allPrescriptions->count() }})
            </button>
            <button
                wire:click="$set('activeTab', 'orders')"
                class="py-3 px-2 border-b-2 font-bold text-sm whitespace-nowrap transition {{ $activeTab === 'orders' ? 'border-[#E82229] text-[#E82229]' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
            >
                Commandes ({{ $allOrders->count() }})
            </button>
            <button
                wire:click="$set('activeTab', 'payments')"
                class="py-3 px-2 border-b-2 font-bold text-sm whitespace-nowrap transition {{ $activeTab === 'payments' ? 'border-[#E82229] text-[#E82229]' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
            >
                Paiements ({{ $allPayments->count() }})
            </button>
            <button
                wire:click="$set('activeTab', 'documents')"
                class="py-3 px-2 border-b-2 font-bold text-sm whitespace-nowrap transition {{ $activeTab === 'documents' ? 'border-[#E82229] text-[#E82229]' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
            >
                Factures & Documents ({{ $allDocuments->count() }})
            </button>
            <button
                wire:click="$set('activeTab', 'reminders')"
                class="py-3 px-2 border-b-2 font-bold text-sm whitespace-nowrap transition {{ $activeTab === 'reminders' ? 'border-[#E82229] text-[#E82229]' : 'border-transparent text-slate-500 hover:text-slate-800' }}"
            >
                Rappels & Suivi ({{ $allReminders->count() }})
            </button>
        </nav>
    </div>

    <!-- Tab 1: Vue d'ensemble (Overview) -->
    @if ($activeTab === 'overview')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column (2 Cols): Latest Prescription & Orders -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Section 1: Dernière Ordonnance (Refraction Grid) -->
                <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-3 h-3 rounded-full bg-[#172B4D]"></div>
                            <h3 class="text-lg font-bold text-[#172B4D]">Dernière Ordonnance en vigueur</h3>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($client->latestPrescription)
                                <span class="text-xs font-semibold text-slate-500 hidden sm:inline mr-1">
                                    Date : <strong class="text-[#172B4D]">{{ $client->latestPrescription->prescription_date->format('d/m/Y') }}</strong>
                                    @if ($client->latestPrescription->prescribed_by)
                                        • {{ $client->latestPrescription->prescribed_by }}
                                    @endif
                                </span>
                                <button
                                    wire:click="openEditPrescriptionModal({{ $client->latestPrescription->id }})"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200/80 text-[#172B4D] border border-slate-300 text-xs font-bold transition shadow-sm hover:shadow-md"
                                    title="Modifier cette ordonnance"
                                >
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>Modifier</span>
                                </button>
                            @endif
                            <button
                                wire:click="openCreatePrescriptionModal"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#E82229] hover:bg-[#c91a21] text-white text-xs font-bold transition shadow-md shadow-[#E82229]/30 hover:shadow-lg hover:shadow-[#E82229]/40"
                                title="Ajouter une nouvelle ordonnance"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Nouvelle</span>
                            </button>
                        </div>
                    </div>

                    @if ($client->latestPrescription)
                        @php $rx = $client->latestPrescription; @endphp
                        <!-- Clinical Prescription Grid with restrained blue/teal styling & explicit labels -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-center border-collapse text-sm">
                                <thead>
                                    <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider">
                                        <th class="py-2.5 px-4 text-left border border-slate-200">Œil</th>
                                        <th class="py-2.5 px-3 border border-slate-200">Sphère (SPH)</th>
                                        <th class="py-2.5 px-3 border border-slate-200">Cylindre (CYL)</th>
                                        <th class="py-2.5 px-3 border border-slate-200">Axe (AX)</th>
                                        <th class="py-2.5 px-3 border border-slate-200">Addition (ADD)</th>
                                        <th class="py-2.5 px-3 border border-slate-200">Écart (PD)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- OD: Right Eye (Oeil Droit) -->
                                    <tr class="bg-teal-50/40 font-semibold text-slate-800">
                                        <td class="py-3 px-4 text-left border border-slate-200 font-bold text-[#172B4D] bg-teal-50/70">
                                            <span class="inline-block w-6 py-0.5 text-center bg-teal-600 text-white rounded text-[10px] mr-1.5">OD</span>
                                            Œil Droit
                                        </td>
                                        <td class="py-3 px-3 border border-slate-200 font-mono text-[#172B4D]">
                                            {{ $rx->formatValue((string)$rx->od_sphere) }}
                                        </td>
                                        <td class="py-3 px-3 border border-slate-200 font-mono text-[#172B4D]">
                                            {{ $rx->formatValue((string)$rx->od_cylinder) }}
                                        </td>
                                        <td class="py-3 px-3 border border-slate-200 font-mono text-[#172B4D]">
                                            {{ $rx->od_axis !== null ? $rx->od_axis . '°' : '-' }}
                                        </td>
                                        <td class="py-3 px-3 border border-slate-200 font-mono text-teal-700 font-bold">
                                            {{ $rx->od_addition ? $rx->formatValue((string)$rx->od_addition) : '-' }}
                                        </td>
                                        <td class="py-3 px-3 border border-slate-200 font-mono text-slate-600">
                                            {{ $rx->pd_right ? $rx->pd_right . ' mm' : '-' }}
                                        </td>
                                    </tr>

                                    <!-- OG: Left Eye (Oeil Gauche) -->
                                    <tr class="bg-sky-50/40 font-semibold text-slate-800">
                                        <td class="py-3 px-4 text-left border border-slate-200 font-bold text-[#172B4D] bg-sky-50/70">
                                            <span class="inline-block w-6 py-0.5 text-center bg-sky-700 text-white rounded text-[10px] mr-1.5">OG</span>
                                            Œil Gauche
                                        </td>
                                        <td class="py-3 px-3 border border-slate-200 font-mono text-[#172B4D]">
                                            {{ $rx->formatValue((string)$rx->og_sphere) }}
                                        </td>
                                        <td class="py-3 px-3 border border-slate-200 font-mono text-[#172B4D]">
                                            {{ $rx->formatValue((string)$rx->og_cylinder) }}
                                        </td>
                                        <td class="py-3 px-3 border border-slate-200 font-mono text-[#172B4D]">
                                            {{ $rx->og_axis !== null ? $rx->og_axis . '°' : '-' }}
                                        </td>
                                        <td class="py-3 px-3 border border-slate-200 font-mono text-sky-700 font-bold">
                                            {{ $rx->og_addition ? $rx->formatValue((string)$rx->og_addition) : '-' }}
                                        </td>
                                        <td class="py-3 px-3 border border-slate-200 font-mono text-slate-600">
                                            {{ $rx->pd_left ? $rx->pd_left . ' mm' : '-' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex flex-wrap items-center justify-between gap-4 mt-4 pt-3 border-t border-slate-100 text-xs text-slate-500">
                            <div class="flex items-center gap-4">
                                <span>Écart pupillaire total : <strong class="text-slate-800">{{ $rx->pd ? $rx->pd . ' mm' : 'Non mesuré' }}</strong></span>
                                @if ($rx->has_file)
                                    <a
                                        href="{{ route('prescriptions.file', $rx) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 text-[#0F9D91] font-bold hover:underline"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span>Ordonnance numérisée</span>
                                    </a>
                                @endif
                            </div>
                            @if ($rx->notes)
                                <div class="italic text-slate-600">
                                    Observation : {{ $rx->notes }}
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="py-8 text-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200 space-y-3">
                            <p class="text-sm font-medium">Aucune ordonnance enregistrée pour ce patient.</p>
                            <button
                                wire:click="openCreatePrescriptionModal"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#E82229] text-white text-xs font-bold hover:bg-[#c91a21] transition"
                            >
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                <span>Ajouter la première ordonnance</span>
                            </button>
                        </div>
                    @endif
                </div>

                <!-- Section 2: Commandes Récentes -->
                <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-3 h-3 rounded-full bg-[#172B4D]"></div>
                            <h3 class="text-lg font-bold text-[#172B4D]">Historique des Commandes</h3>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                                    <th class="py-3 px-3">N° Commande</th>
                                    <th class="py-3 px-3">Date</th>
                                    <th class="py-3 px-3">Articles</th>
                                    <th class="py-3 px-3">Total TTC</th>
                                    <th class="py-3 px-3">Reste</th>
                                    <th class="py-3 px-3">État</th>
                                    <th class="py-3 px-3 text-right">Paiement</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($allOrders as $order)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="py-3.5 px-3 font-mono font-bold text-[#172B4D]">
                                            {{ $order->order_number }}
                                        </td>
                                        <td class="py-3.5 px-3 text-slate-600 text-xs">
                                            {{ $order->order_date->format('d/m/Y') }}
                                        </td>
                                        <td class="py-3.5 px-3 text-slate-700 text-xs max-w-xs truncate">
                                            {{ $order->items->pluck('description')->implode(', ') ?: 'N/A' }}
                                        </td>
                                        <td class="py-3.5 px-3 font-bold text-[#172B4D]">
                                            {{ number_format((float) $order->total_amount, 2, ',', ' ') }} {{ $storeSetting->currency }}
                                        </td>
                                        <td class="py-3.5 px-3 font-bold {{ $order->remaining_amount > 0 ? 'text-[#E82229]' : 'text-[#0F9D91]' }}">
                                            {{ number_format((float) $order->remaining_amount, 2, ',', ' ') }}
                                        </td>
                                        <td class="py-3.5 px-3">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $order->status->badgeClasses() }}">
                                                {{ $order->status->label() }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-3 text-right">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $order->payment_status->badgeClasses() }}">
                                                {{ $order->payment_status->label() }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="py-8 text-center text-slate-400">
                                            Aucune commande enregistrée pour ce client.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column (1 Col): Reminders & Clinical Notes -->
            <div class="space-y-8">
                <!-- Section 3: Notes & Observations Cliniques -->
                <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#172B4D]"></span>
                            <h3 class="text-base font-bold text-[#172B4D]">Notes & Antécédents</h3>
                        </div>
                        @if (!$isEditingNotes)
                            <button wire:click="$set('isEditingNotes', true)" class="text-xs font-bold text-[#172B4D] hover:text-[#E82229] transition">
                                Modifier
                            </button>
                        @endif
                    </div>

                    @if ($isEditingNotes)
                        <div class="space-y-3">
                            <textarea wire:model="notes" rows="4" placeholder="Ajouter une observation clinique, préférences du patient, mutuelle..." class="w-full p-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 text-sm focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none"></textarea>
                            <div class="flex items-center justify-end gap-2">
                                <button wire:click="$set('isEditingNotes', false)" class="px-3 py-1.5 rounded-lg border border-slate-300 text-xs font-semibold text-slate-600">Annuler</button>
                                <button wire:click="saveNotes" class="px-3 py-1.5 rounded-lg bg-[#172B4D] text-white text-xs font-bold shadow-xs">Enregistrer</button>
                            </div>
                        </div>
                    @else
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-sm text-slate-700">
                            {{ $client->notes ?: 'Aucune observation enregistrée.' }}
                        </div>
                    @endif
                </div>

                <!-- Section 4: Rappels & Suivi Patient -->
                <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 mb-4">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <h3 class="text-base font-bold text-[#172B4D]">Rappels & Alertes</h3>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @forelse ($allReminders as $reminder)
                            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200/80">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-amber-800">{{ $reminder->reminder_type->label() }}</span>
                                    <span class="text-[11px] font-medium text-slate-500">{{ $reminder->reminder_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="mt-1 text-xs text-slate-600 font-medium">
                                    État : <strong class="text-slate-800">{{ $reminder->status->label() }}</strong>
                                </div>
                                @if ($reminder->notes)
                                    <p class="text-xs text-slate-500 mt-1 italic">{{ $reminder->notes }}</p>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 py-3 text-center">Aucun rappel programmé pour ce patient.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Tab 2: Ordonnances (Prescriptions Tab) -->
    @if ($activeTab === 'prescriptions')
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs space-y-6">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-lg font-bold text-[#172B4D]">Historique complet des Réfractions & Ordonnances</h3>
                    <p class="text-xs text-slate-500">Toutes les prescriptions enregistrées pour ce patient, de la plus récente à la plus ancienne.</p>
                </div>
                <button
                    wire:click="openCreatePrescriptionModal"
                    class="px-4 py-2 rounded-xl bg-[#E82229] hover:bg-[#c91a21] text-white text-xs font-bold shadow-md shadow-[#E82229]/20 transition flex items-center gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Ajouter une ordonnance</span>
                </button>
            </div>

            <div class="space-y-6">
                @forelse ($allPrescriptions as $prescription)
                    <div class="p-5 rounded-2xl border border-slate-200 bg-slate-50/50 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#172B4D]"></span>
                                <span class="font-bold text-base text-[#172B4D]">Ordonnance du {{ $prescription->prescription_date->format('d/m/Y') }}</span>
                                <span class="text-xs text-slate-400 font-mono">#ORD-{{ sprintf('%05d', $prescription->id) }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                @if ($prescription->prescribed_by)
                                    <span class="text-xs font-semibold text-slate-600 bg-white border border-slate-200 px-2.5 py-1 rounded-lg">
                                        Dr. {{ $prescription->prescribed_by }}
                                    </span>
                                @endif
                                @if ($prescription->has_file)
                                    <a
                                        href="{{ route('prescriptions.file', $prescription) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-50 text-[#0F9D91] border border-teal-200 text-xs font-bold hover:bg-teal-100 transition"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span>Document</span>
                                    </a>
                                @endif
                                <button
                                    wire:click="openEditPrescriptionModal({{ $prescription->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-white hover:bg-slate-100 text-[#172B4D] border border-slate-200 text-xs font-bold transition shadow-2xs"
                                >
                                    <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    <span>Modifier</span>
                                </button>
                                <button
                                    wire:click="confirmDeletePrescription({{ $prescription->id }})"
                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-[#E82229] border border-rose-200/80 text-xs font-bold transition shadow-2xs"
                                >
                                    <span>Archiver</span>
                                </button>
                            </div>
                        </div>

                        <!-- Grid -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-center border-collapse text-sm bg-white rounded-xl overflow-hidden shadow-xs">
                                <thead>
                                    <tr class="bg-slate-100 text-slate-700 text-xs font-bold uppercase tracking-wider">
                                        <th class="py-2.5 px-4 text-left border border-slate-200">Œil</th>
                                        <th class="py-2.5 px-3 border border-slate-200">Sphère (SPH)</th>
                                        <th class="py-2.5 px-3 border border-slate-200">Cylindre (CYL)</th>
                                        <th class="py-2.5 px-3 border border-slate-200">Axe (AXE °)</th>
                                        <th class="py-2.5 px-3 border border-slate-200">Addition (ADD)</th>
                                        <th class="py-2.5 px-3 border border-slate-200">Demi-Écart (EP)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-teal-50/40">
                                        <td class="py-2.5 px-4 text-left border border-slate-200 font-bold text-[#172B4D]">
                                            <span class="inline-block w-6 py-0.5 text-center bg-teal-600 text-white rounded text-[10px] mr-1.5">OD</span>
                                            Œil Droit
                                        </td>
                                        <td class="py-2.5 px-3 border border-slate-200 font-mono font-bold text-[#172B4D]">{{ $prescription->formatValue((string)$prescription->od_sphere) }}</td>
                                        <td class="py-2.5 px-3 border border-slate-200 font-mono font-bold text-[#172B4D]">{{ $prescription->formatValue((string)$prescription->od_cylinder) }}</td>
                                        <td class="py-2.5 px-3 border border-slate-200 font-mono font-bold text-[#172B4D]">{{ $prescription->od_axis !== null ? $prescription->od_axis . '°' : '-' }}</td>
                                        <td class="py-2.5 px-3 border border-slate-200 font-mono font-bold text-teal-700">{{ $prescription->od_addition ? $prescription->formatValue((string)$prescription->od_addition) : '-' }}</td>
                                        <td class="py-2.5 px-3 border border-slate-200 font-mono text-slate-600">{{ $prescription->pd_right ? $prescription->pd_right . ' mm' : '-' }}</td>
                                    </tr>
                                    <tr class="bg-sky-50/40">
                                        <td class="py-2.5 px-4 text-left border border-slate-200 font-bold text-[#172B4D]">
                                            <span class="inline-block w-6 py-0.5 text-center bg-sky-700 text-white rounded text-[10px] mr-1.5">OG</span>
                                            Œil Gauche
                                        </td>
                                        <td class="py-2.5 px-3 border border-slate-200 font-mono font-bold text-[#172B4D]">{{ $prescription->formatValue((string)$prescription->og_sphere) }}</td>
                                        <td class="py-2.5 px-3 border border-slate-200 font-mono font-bold text-[#172B4D]">{{ $prescription->formatValue((string)$prescription->og_cylinder) }}</td>
                                        <td class="py-2.5 px-3 border border-slate-200 font-mono font-bold text-[#172B4D]">{{ $prescription->og_axis !== null ? $prescription->og_axis . '°' : '-' }}</td>
                                        <td class="py-2.5 px-3 border border-slate-200 font-mono font-bold text-sky-700">{{ $prescription->og_addition ? $prescription->formatValue((string)$prescription->og_addition) : '-' }}</td>
                                        <td class="py-2.5 px-3 border border-slate-200 font-mono text-slate-600">{{ $prescription->pd_left ? $prescription->pd_left . ' mm' : '-' }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="flex flex-wrap items-center justify-between text-xs text-slate-500 pt-1">
                            <div>Écart pupillaire total : <strong class="text-slate-800">{{ $prescription->pd ? $prescription->pd . ' mm' : '-' }}</strong></div>
                            @if ($prescription->notes)
                                <div class="italic text-slate-600">Note : {{ $prescription->notes }}</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 space-y-3">
                        <p class="font-bold text-slate-600">Aucune ordonnance enregistrée.</p>
                        <p class="text-xs text-slate-400">Cliquez ci-dessous pour ajouter la première prescription.</p>
                        <button
                            wire:click="openCreatePrescriptionModal"
                            class="px-4 py-2 rounded-xl bg-[#E82229] hover:bg-[#c91a21] text-white text-xs font-bold shadow-md shadow-[#E82229]/20 transition"
                        >
                            Nouvelle Ordonnance
                        </button>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- Tab 3: Commandes (Orders Tab) -->
    @if ($activeTab === 'orders')
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <h3 class="text-lg font-bold text-[#172B4D]">Historique complet des commandes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3 px-3">N° Commande</th>
                            <th class="py-3 px-3">Date</th>
                            <th class="py-3 px-3">Total TTC</th>
                            <th class="py-3 px-3">Avance payée</th>
                            <th class="py-3 px-3">Solde restant</th>
                            <th class="py-3 px-3">État</th>
                            <th class="py-3 px-3 text-right">Paiement</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($allOrders as $order)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3 font-mono font-bold text-[#172B4D]">{{ $order->order_number }}</td>
                                <td class="py-3.5 px-3 text-slate-600 text-xs">{{ $order->order_date->format('d/m/Y') }}</td>
                                <td class="py-3.5 px-3 font-bold text-[#172B4D]">{{ number_format((float) $order->total_amount, 2, ',', ' ') }} {{ $storeSetting->currency }}</td>
                                <td class="py-3.5 px-3 font-bold text-[#0F9D91]">{{ number_format((float) $order->amount_paid, 2, ',', ' ') }}</td>
                                <td class="py-3.5 px-3 font-bold {{ $order->remaining_amount > 0 ? 'text-[#E82229]' : 'text-slate-400' }}">{{ number_format((float) $order->remaining_amount, 2, ',', ' ') }}</td>
                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $order->status->badgeClasses() }}">
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold border {{ $order->payment_status->badgeClasses() }}">
                                        {{ $order->payment_status->label() }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">Aucune commande enregistrée.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Tab 4: Paiements -->
    @if ($activeTab === 'payments')
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <h3 class="text-lg font-bold text-[#172B4D]">Historique des Règlements & Paiements</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3 px-3">Date</th>
                            <th class="py-3 px-3">N° Commande</th>
                            <th class="py-3 px-3">Montant Encaissé</th>
                            <th class="py-3 px-3">Mode de Paiement</th>
                            <th class="py-3 px-3 text-right">Référence</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($allPayments as $pay)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3 text-xs text-slate-600">{{ $pay->payment_date->format('d/m/Y') }}</td>
                                <td class="py-3.5 px-3 font-mono font-bold text-[#172B4D]">{{ $pay->order->order_number ?? 'N/A' }}</td>
                                <td class="py-3.5 px-3 font-bold text-[#0F9D91]">{{ number_format((float) $pay->amount, 2, ',', ' ') }} {{ $storeSetting->currency }}</td>
                                <td class="py-3.5 px-3">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                        {{ $pay->payment_method->label() }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-3 text-right text-xs text-slate-500 font-mono">{{ $pay->reference ?: '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Aucun paiement enregistré pour ce client.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Tab 5: Documents -->
    @if ($activeTab === 'documents')
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-5">
                <h3 class="text-lg font-bold text-[#172B4D]">Factures, Devis & Bons de Commande</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-100 bg-slate-50/50">
                            <th class="py-3 px-3">Type</th>
                            <th class="py-3 px-3">N° Document</th>
                            <th class="py-3 px-3">Date</th>
                            <th class="py-3 px-3">Commande Liée</th>
                            <th class="py-3 px-3 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($allDocuments as $doc)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-3.5 px-3 font-bold text-[#172B4D]">{{ $doc->document_type->label() }}</td>
                                <td class="py-3.5 px-3 font-mono text-xs font-bold text-slate-700">{{ $doc->document_number }}</td>
                                <td class="py-3.5 px-3 text-xs text-slate-600">{{ $doc->created_at->format('d/m/Y H:i') }}</td>
                                <td class="py-3.5 px-3 font-mono text-xs text-slate-600">{{ $doc->order->order_number ?? 'N/A' }}</td>
                                <td class="py-3.5 px-3 text-right">
                                    <a href="#" class="text-xs font-bold text-[#E82229] hover:underline">Télécharger PDF</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-slate-400">Aucun document comptable généré pour ce client.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Tab 6: Rappels -->
    @if ($activeTab === 'reminders')
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-2">
                <h3 class="text-lg font-bold text-[#172B4D]">Suivi & Rappels Patients</h3>
            </div>
            <div class="space-y-3">
                @forelse ($allReminders as $rem)
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex items-center justify-between">
                        <div>
                            <span class="text-xs font-bold uppercase tracking-wider text-amber-800">{{ $rem->reminder_type->label() }}</span>
                            <p class="text-sm font-semibold text-[#172B4D] mt-0.5">Date prévue : {{ $rem->reminder_date->format('d/m/Y') }}</p>
                            @if ($rem->notes)
                                <p class="text-xs text-slate-500 mt-1 italic">{{ $rem->notes }}</p>
                            @endif
                        </div>
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                                {{ $rem->status->label() }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="py-8 text-center text-slate-400">Aucun rappel enregistré.</p>
                @endforelse
            </div>
        </div>
    @endif

    <!-- EDIT CLIENT MODAL -->
    @if ($showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 my-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <h3 class="text-xl font-bold text-[#172B4D]">Modifier la Fiche Patient</h3>
                    <button wire:click="$set('showEditModal', false)" class="text-slate-400 hover:text-slate-600 p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="updateClient" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Prénom <span class="text-[#E82229]">*</span></label>
                            <input type="text" wire:model="first_name" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm font-medium">
                            @error('first_name') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nom <span class="text-[#E82229]">*</span></label>
                            <input type="text" wire:model="last_name" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm font-medium">
                            @error('last_name') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Téléphone <span class="text-[#E82229]">*</span></label>
                            <input type="text" wire:model="phone" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm font-semibold">
                            @error('phone') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Email</label>
                            <input type="email" wire:model="email" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm">
                            @error('email') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Genre</label>
                            <select wire:model="gender" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm font-medium">
                                <option value="">-- Non spécifié --</option>
                                <option value="male">Homme</option>
                                <option value="female">Femme</option>
                                <option value="child">Enfant</option>
                            </select>
                            @error('gender') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Date de naissance</label>
                            <input type="date" wire:model="date_of_birth" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm">
                            @error('date_of_birth') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Adresse</label>
                            <input type="text" wire:model="address" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm">
                            @error('address') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Ville</label>
                            <input type="text" wire:model="city" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm font-medium">
                            @error('city') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button type="button" wire:click="$set('showEditModal', false)" class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-semibold text-sm transition">
                            Annuler
                        </button>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-[#E82229] hover:bg-[#D0181F] text-white font-bold text-sm shadow-md shadow-[#E82229]/20 transition">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- PRESCRIPTION CREATE / EDIT MODAL FOR CLIENT -->
    @if ($showPrescriptionModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-3xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-xl font-black text-[#172B4D]">
                            {{ $editingPrescriptionId ? 'Modifier l\'ordonnance' : 'Nouvelle Ordonnance' }} — {{ $client->full_name }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Saisie des corrections optiques et mesures pupillaires</p>
                    </div>
                    <button wire:click="$set('showPrescriptionModal', false)" class="text-slate-400 hover:text-slate-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit.prevent="savePrescription" class="space-y-6">
                    <!-- Date & Doctor -->
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-4.5 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Date de l'ordonnance <span class="text-rose-500">*</span></label>
                                <input
                                    type="date"
                                    wire:model="prescription_date"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm focus:border-[#172B4D] focus:ring-1 focus:ring-[#172B4D]"
                                >
                                @error('prescription_date') <span class="text-xs text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Prescripteur (Médecin / Ophtalmologue)</label>
                                <input
                                    type="text"
                                    wire:model="prescribed_by"
                                    placeholder="Ex: Dr. Bennani"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm focus:border-[#172B4D] focus:ring-1 focus:ring-[#172B4D]"
                                >
                                @error('prescribed_by') <span class="text-xs text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Refraction Grid OD / OG -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black uppercase tracking-wider text-[#172B4D] flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#0F9D91]"></span>
                                <span>Formule Réfractive (Correction Optique)</span>
                            </h4>
                            <span class="text-[11px] text-slate-400">Pas de 0.25 (SPH, CYL, ADD) & 0°-180° (AXE)</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- OD -->
                            <div class="bg-teal-50/40 border-2 border-teal-500/30 rounded-2xl p-4.5 space-y-4">
                                <div class="flex items-center justify-between pb-2 border-b border-teal-200/60">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-lg bg-teal-600 text-white flex items-center justify-center text-xs font-black">OD</span>
                                        <span class="font-black text-sm text-[#172B4D]">Œil Droit (OD)</span>
                                    </div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-teal-700 bg-teal-100 px-2 py-0.5 rounded-full">Oculus Dexter</span>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Sphère (SPH)</label>
                                        <input
                                            type="number"
                                            step="0.25"
                                            wire:model="od_sphere"
                                            placeholder="Ex: -2.25"
                                            class="w-full px-3 py-2 bg-white border border-teal-200 rounded-xl text-sm font-mono text-[#172B4D] focus:border-teal-600 focus:ring-1 focus:ring-teal-600"
                                        >
                                        @error('od_sphere') <span class="text-[10px] text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Cylindre (CYL)</label>
                                        <input
                                            type="number"
                                            step="0.25"
                                            wire:model="od_cylinder"
                                            placeholder="Ex: -0.75"
                                            class="w-full px-3 py-2 bg-white border border-teal-200 rounded-xl text-sm font-mono text-[#172B4D] focus:border-teal-600 focus:ring-1 focus:ring-teal-600"
                                        >
                                        @error('od_cylinder') <span class="text-[10px] text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Axe (AXE °)</label>
                                        <input
                                            type="number"
                                            step="1"
                                            min="0"
                                            max="180"
                                            wire:model="od_axis"
                                            placeholder="Ex: 90"
                                            class="w-full px-3 py-2 bg-white border border-teal-200 rounded-xl text-sm font-mono text-[#172B4D] focus:border-teal-600 focus:ring-1 focus:ring-teal-600"
                                        >
                                        @error('od_axis') <span class="text-[10px] text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Addition (ADD)</label>
                                        <input
                                            type="number"
                                            step="0.25"
                                            min="0"
                                            max="10"
                                            wire:model="od_addition"
                                            placeholder="Ex: +1.50"
                                            class="w-full px-3 py-2 bg-white border border-teal-200 rounded-xl text-sm font-mono text-[#172B4D] focus:border-teal-600 focus:ring-1 focus:ring-teal-600"
                                        >
                                        @error('od_addition') <span class="text-[10px] text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- OG -->
                            <div class="bg-sky-50/40 border-2 border-sky-500/30 rounded-2xl p-4.5 space-y-4">
                                <div class="flex items-center justify-between pb-2 border-b border-sky-200/60">
                                    <div class="flex items-center gap-2">
                                        <span class="w-7 h-7 rounded-lg bg-sky-700 text-white flex items-center justify-center text-xs font-black">OG</span>
                                        <span class="font-black text-sm text-[#172B4D]">Œil Gauche (OG)</span>
                                    </div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-sky-700 bg-sky-100 px-2 py-0.5 rounded-full">Oculus Sinister</span>
                                </div>

                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Sphère (SPH)</label>
                                        <input
                                            type="number"
                                            step="0.25"
                                            wire:model="og_sphere"
                                            placeholder="Ex: -1.75"
                                            class="w-full px-3 py-2 bg-white border border-sky-200 rounded-xl text-sm font-mono text-[#172B4D] focus:border-sky-600 focus:ring-1 focus:ring-sky-600"
                                        >
                                        @error('og_sphere') <span class="text-[10px] text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Cylindre (CYL)</label>
                                        <input
                                            type="number"
                                            step="0.25"
                                            wire:model="og_cylinder"
                                            placeholder="Ex: -0.50"
                                            class="w-full px-3 py-2 bg-white border border-sky-200 rounded-xl text-sm font-mono text-[#172B4D] focus:border-sky-600 focus:ring-1 focus:ring-sky-600"
                                        >
                                        @error('og_cylinder') <span class="text-[10px] text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Axe (AXE °)</label>
                                        <input
                                            type="number"
                                            step="1"
                                            min="0"
                                            max="180"
                                            wire:model="og_axis"
                                            placeholder="Ex: 85"
                                            class="w-full px-3 py-2 bg-white border border-sky-200 rounded-xl text-sm font-mono text-[#172B4D] focus:border-sky-600 focus:ring-1 focus:ring-sky-600"
                                        >
                                        @error('og_axis') <span class="text-[10px] text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-bold text-slate-700 mb-1">Addition (ADD)</label>
                                        <input
                                            type="number"
                                            step="0.25"
                                            min="0"
                                            max="10"
                                            wire:model="og_addition"
                                            placeholder="Ex: +1.50"
                                            class="w-full px-3 py-2 bg-white border border-sky-200 rounded-xl text-sm font-mono text-[#172B4D] focus:border-sky-600 focus:ring-1 focus:ring-sky-600"
                                        >
                                        @error('og_addition') <span class="text-[10px] text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pupillary Distances -->
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-4.5 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black uppercase tracking-wider text-[#172B4D] flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                <span>Écarts Pupillaires (EP en mm)</span>
                            </h4>
                            <span class="text-[11px] text-slate-400">Total automatique si OD + OG</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Demi-écart Droit (EP OD)</label>
                                <input
                                    type="number"
                                    step="0.5"
                                    wire:model.live="pd_right"
                                    placeholder="Ex: 31.5"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-mono text-[#172B4D]"
                                >
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Demi-écart Gauche (EP OG)</label>
                                <input
                                    type="number"
                                    step="0.5"
                                    wire:model.live="pd_left"
                                    placeholder="Ex: 31.0"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-mono text-[#172B4D]"
                                >
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Écart Total (EP Total)</label>
                                <input
                                    type="number"
                                    step="0.5"
                                    wire:model="pd"
                                    placeholder="Ex: 62.5"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-[#172B4D]"
                                >
                            </div>
                        </div>
                    </div>

                    <!-- Notes & Upload -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Notes & Recommandations</label>
                            <textarea
                                wire:model="prescription_notes"
                                rows="3"
                                placeholder="Verres recommandés, filtres anti-lumière bleue..."
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm focus:border-[#172B4D]"
                            ></textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Document Numérisé (PDF / Image)</label>
                            <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center bg-slate-50/50">
                                @if ($existing_prescription_file_path)
                                    <div class="flex items-center justify-between p-2 bg-white rounded-xl border border-slate-200 text-xs mb-2">
                                        <span class="font-bold text-[#0F9D91]">Fichier attaché existant</span>
                                        <button
                                            type="button"
                                            wire:click="deleteAttachedPrescriptionFile"
                                            class="text-rose-500 font-bold hover:underline"
                                        >
                                            Supprimer
                                        </button>
                                    </div>
                                @endif

                                <input
                                    type="file"
                                    wire:model="prescription_file"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#172B4D] file:text-white"
                                >
                                <p class="text-[10px] text-slate-400 mt-1">PDF, JPG, PNG jusqu'à 10 Mo.</p>
                                @error('prescription_file') <span class="text-xs text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button
                            type="button"
                            wire:click="$set('showPrescriptionModal', false)"
                            class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-bold hover:bg-slate-100 transition"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-xl bg-[#E82229] hover:bg-[#c91a21] text-white text-sm font-bold shadow-md shadow-[#E82229]/20 transition"
                        >
                            {{ $editingPrescriptionId ? 'Mettre à jour' : 'Enregistrer dans le dossier' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- DELETE PRESCRIPTION CONFIRMATION MODAL -->
    @if ($showDeletePrescriptionModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#E82229] flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-[#172B4D]">Archiver cette ordonnance</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Êtes-vous sûr de vouloir archiver cette ordonnance ? Elle ne sera plus active mais l'historique sera conservé.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3">
                    <button
                        wire:click="$set('showDeletePrescriptionModal', false)"
                        class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-100 transition"
                    >
                        Annuler
                    </button>
                    <button
                        wire:click="deletePrescription"
                        class="px-4 py-2 rounded-xl bg-[#E82229] hover:bg-[#c91a21] text-white text-xs font-bold transition"
                    >
                        Confirmer
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

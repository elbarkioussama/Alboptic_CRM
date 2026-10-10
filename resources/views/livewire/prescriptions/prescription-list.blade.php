<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#172B4D] tracking-tight">Ordonnances & Réfractions</h1>
            <p class="text-sm text-slate-500 mt-1">Historique des examens de vue, réfractions OD/OG et ordonnances ophtalmologiques.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                wire:click="openCreateModal"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-[#E82229] hover:bg-[#c91a21] text-white text-sm font-bold shadow-md shadow-[#E82229]/20 transition active:scale-[0.98]"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Nouvelle Ordonnance</span>
            </button>
        </div>
    </div>

    <!-- Active Client Filter Banner (if filtering by client) -->
    @if ($activeFilterClient)
        <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-[#172B4D] text-white flex items-center justify-center font-bold text-sm">
                    {{ strtoupper(substr($activeFilterClient->first_name, 0, 1) . substr($activeFilterClient->last_name, 0, 1)) }}
                </div>
                <div>
                    <span class="text-xs text-blue-600 font-bold uppercase tracking-wider block">Filtre Patient Actif</span>
                    <h4 class="text-sm font-black text-[#172B4D]">{{ $activeFilterClient->full_name }} — {{ $activeFilterClient->phone }}</h4>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('clients.show', $activeFilterClient) }}" class="text-xs font-bold text-[#172B4D] hover:underline px-3 py-1.5 bg-white rounded-lg border border-slate-200">
                    Voir Fiche Client
                </a>
                <button wire:click="$set('clientIdFilter', null)" class="text-xs font-bold text-rose-600 hover:underline px-2 py-1.5">
                    Effacer le filtre
                </button>
            </div>
        </div>
    @endif

    <!-- Search & Filter Controls -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 shadow-xs">
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <div class="relative w-full sm:flex-1">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Recherche par nom du patient, n° téléphone, médecin prescripteur..."
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 focus:border-[#172B4D] focus:ring-2 focus:ring-[#172B4D]/10 text-sm text-[#172B4D] placeholder-slate-400 bg-slate-50/50"
                >
                <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            @if ($search || $clientIdFilter)
                <button
                    wire:click="$set('search', ''); $set('clientIdFilter', null)"
                    class="text-xs font-bold text-slate-500 hover:text-[#E82229] transition px-3 py-2"
                >
                    Réinitialiser
                </button>
            @endif
        </div>
    </div>

    <!-- Prescriptions Table -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200/80 text-[11px] font-black uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4">Date & N°</th>
                        <th class="py-3.5 px-4">Patient / Client</th>
                        <th class="py-3.5 px-4">Prescripteur</th>
                        <th class="py-3.5 px-4">Œil Droit (OD)</th>
                        <th class="py-3.5 px-4">Œil Gauche (OG)</th>
                        <th class="py-3.5 px-4 text-center">Écarts (EP)</th>
                        <th class="py-3.5 px-4 text-center">Document</th>
                        <th class="py-3.5 px-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse ($prescriptions as $prescription)
                        <tr class="hover:bg-slate-50/60 transition group">
                            <!-- Date & ID -->
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-[#172B4D]">{{ $prescription->prescription_date->format('d/m/Y') }}</div>
                                <span class="text-[11px] font-mono text-slate-400">#ORD-{{ sprintf('%05d', $prescription->id) }}</span>
                            </td>

                            <!-- Patient -->
                            <td class="py-3.5 px-4">
                                <a href="{{ route('clients.show', $prescription->client) }}" class="font-bold text-[#172B4D] hover:text-[#0F9D91] transition block">
                                    {{ $prescription->client->full_name }}
                                </a>
                                <span class="text-xs text-slate-500 font-mono">{{ $prescription->client->phone }}</span>
                            </td>

                            <!-- Prescribed By -->
                            <td class="py-3.5 px-4">
                                @if ($prescription->prescribed_by)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold">
                                        <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ $prescription->prescribed_by }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Non précisé (Magasin)</span>
                                @endif
                            </td>

                            <!-- OD -->
                            <td class="py-3.5 px-4">
                                <div class="font-mono text-xs font-semibold text-slate-800 bg-teal-50/50 border border-teal-100/80 px-2.5 py-1.5 rounded-lg max-w-[200px]">
                                    {{ $prescription->od_summary }}
                                </div>
                            </td>

                            <!-- OG -->
                            <td class="py-3.5 px-4">
                                <div class="font-mono text-xs font-semibold text-slate-800 bg-sky-50/50 border border-sky-100/80 px-2.5 py-1.5 rounded-lg max-w-[200px]">
                                    {{ $prescription->og_summary }}
                                </div>
                            </td>

                            <!-- PD -->
                            <td class="py-3.5 px-4 text-center">
                                @if ($prescription->pd || $prescription->pd_right || $prescription->pd_left)
                                    <div class="font-mono text-xs font-bold text-[#172B4D]">
                                        @if ($prescription->pd)
                                            <span>{{ $prescription->pd }} mm</span>
                                        @endif
                                        @if ($prescription->pd_right || $prescription->pd_left)
                                            <span class="text-[10px] text-slate-400 block">({{ $prescription->pd_right ?? '-' }} / {{ $prescription->pd_left ?? '-' }})</span>
                                        @endif
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400">-</span>
                                @endif
                            </td>

                            <!-- Document / File -->
                            <td class="py-3.5 px-4 text-center">
                                @if ($prescription->has_file)
                                    <a
                                        href="{{ route('prescriptions.file', $prescription) }}"
                                        target="_blank"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-teal-50 text-[#0F9D91] hover:bg-teal-100 border border-teal-200 text-xs font-bold transition"
                                        title="Voir l'ordonnance numérisée"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                        </svg>
                                        <span>Fichier</span>
                                    </a>
                                @else
                                    <span class="text-xs text-slate-300">—</span>
                                @endif
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-right">
                                <div class="inline-flex items-center gap-1.5">
                                    <button
                                        wire:click="viewDetails({{ $prescription->id }})"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-[#172B4D] hover:bg-slate-100 transition"
                                        title="Détails complets de la réfraction"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="openEditModal({{ $prescription->id }})"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-[#0F9D91] hover:bg-slate-100 transition"
                                        title="Modifier l'ordonnance"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <button
                                        wire:click="confirmDelete({{ $prescription->id }})"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-[#E82229] hover:bg-rose-50 transition"
                                        title="Supprimer"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <div class="max-w-xs mx-auto text-center space-y-3">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </div>
                                    <p class="font-bold text-slate-700">Aucune ordonnance trouvée</p>
                                    <p class="text-xs text-slate-400">Enregistrez les prescriptions optiques de vos clients pour suivre l'évolution de leur vue.</p>
                                    <button
                                        wire:click="openCreateModal"
                                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-[#E82229] text-white text-xs font-bold hover:bg-[#c91a21] transition"
                                    >
                                        <span>Ajouter une ordonnance</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($prescriptions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $prescriptions->links() }}
            </div>
        @endif
    </div>

    <!-- CREATE / EDIT MODAL -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-3xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6 max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <h3 class="text-xl font-black text-[#172B4D]">
                            {{ $editingPrescriptionId ? 'Modifier l\'ordonnance' : 'Nouvelle Ordonnance Optique' }}
                        </h3>
                        <p class="text-xs text-slate-500 mt-0.5">Saisie des corrections optiques et mesures pupillaires</p>
                    </div>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Form Body -->
                <form wire:submit.prevent="savePrescription" class="space-y-6">
                    <!-- General Information (Client, Date, Prescribed By) -->
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-4.5 space-y-4">
                        <h4 class="text-xs font-black uppercase tracking-wider text-[#172B4D] flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-[#172B4D]"></span>
                            <span>Informations Générales</span>
                        </h4>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <!-- Client Selection -->
                            <div class="sm:col-span-1 relative">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Patient / Client <span class="text-rose-500">*</span></label>
                                @if ($client_id)
                                    <div class="flex items-center justify-between p-2.5 bg-white border border-slate-300 rounded-xl">
                                        <span class="text-sm font-bold text-[#172B4D] truncate">{{ $client_search }}</span>
                                        <button
                                            type="button"
                                            wire:click="$set('client_id', null); $set('client_search', '')"
                                            class="text-xs text-rose-500 font-bold hover:underline ml-2"
                                        >
                                            Changer
                                        </button>
                                    </div>
                                @else
                                    <input
                                        type="text"
                                        wire:model.live.debounce.250ms="client_search"
                                        placeholder="Taper nom ou téléphone..."
                                        class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm focus:border-[#172B4D] focus:ring-1 focus:ring-[#172B4D]"
                                    >
                                    @if (!empty($clientSuggestions))
                                        <div class="absolute left-0 right-0 top-full mt-1 bg-white border border-slate-200 rounded-xl shadow-lg z-20 max-h-48 overflow-y-auto divide-y divide-slate-100">
                                            @foreach ($clientSuggestions as $sClient)
                                                <button
                                                    type="button"
                                                    wire:click="selectClient({{ $sClient->id }}, '{{ addslashes($sClient->full_name) }}')"
                                                    class="w-full text-left p-2.5 hover:bg-slate-50 transition text-xs flex items-center justify-between"
                                                >
                                                    <span class="font-bold text-[#172B4D]">{{ $sClient->full_name }}</span>
                                                    <span class="text-slate-400 font-mono">{{ $sClient->phone }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endif
                                @endif
                                @error('client_id') <span class="text-xs text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Prescription Date -->
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Date d'ordonnance <span class="text-rose-500">*</span></label>
                                <input
                                    type="date"
                                    wire:model="prescription_date"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm focus:border-[#172B4D] focus:ring-1 focus:ring-[#172B4D]"
                                >
                                @error('prescription_date') <span class="text-xs text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>

                            <!-- Prescribed By -->
                            <div class="sm:col-span-1">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Prescripteur (Médecin / Dr)</label>
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

                    <!-- REFRACTION GRID: OD vs OG visually separated -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black uppercase tracking-wider text-[#172B4D] flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-[#0F9D91]"></span>
                                <span>Formule Réfractive (Correction Optique)</span>
                            </h4>
                            <span class="text-[11px] text-slate-400">Valeurs en dioptries (pas de 0.25) & degrés (0°-180°)</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- OD (Right Eye - Teal Accent) -->
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

                            <!-- OG (Left Eye - Blue/Navy Accent) -->
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

                    <!-- Pupillary Distances (Écart Pupillaire) -->
                    <div class="bg-slate-50/70 border border-slate-200/80 rounded-2xl p-4.5 space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-black uppercase tracking-wider text-[#172B4D] flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                <span>Écarts Pupillaires (EP en mm)</span>
                            </h4>
                            <span class="text-[11px] text-slate-400">Calcul automatique du total si OD + OG saisis</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Demi-écart Droit (EP OD)</label>
                                <input
                                    type="number"
                                    step="0.5"
                                    wire:model.live="pd_right"
                                    placeholder="Ex: 31.5"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-mono text-[#172B4D] focus:border-[#172B4D]"
                                >
                                @error('pd_right') <span class="text-[10px] text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Demi-écart Gauche (EP OG)</label>
                                <input
                                    type="number"
                                    step="0.5"
                                    wire:model.live="pd_left"
                                    placeholder="Ex: 31.0"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-mono text-[#172B4D] focus:border-[#172B4D]"
                                >
                                @error('pd_left') <span class="text-[10px] text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-700 mb-1">Écart Total (EP Total)</label>
                                <input
                                    type="number"
                                    step="0.5"
                                    wire:model="pd"
                                    placeholder="Ex: 62.5"
                                    class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm font-mono font-bold text-[#172B4D] focus:border-[#172B4D]"
                                >
                                @error('pd') <span class="text-[10px] text-rose-600 block mt-0.5">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Document Upload & Notes -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Notes -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Notes & Conseils Optiques</label>
                            <textarea
                                wire:model="notes"
                                rows="3"
                                placeholder="Recommandations verres progressifs, traitements anti-reflet, prismes..."
                                class="w-full px-3 py-2 bg-white border border-slate-300 rounded-xl text-sm focus:border-[#172B4D]"
                            ></textarea>
                            @error('notes') <span class="text-xs text-rose-600 block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Secure File Upload -->
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Document Numérisé (PDF / Image)</label>
                            <div class="border-2 border-dashed border-slate-200 rounded-2xl p-4 text-center hover:border-slate-400 transition bg-slate-50/50">
                                @if ($existing_file_path)
                                    <div class="flex items-center justify-between p-2 bg-white rounded-xl border border-slate-200 text-xs">
                                        <span class="font-bold text-[#0F9D91] truncate">Fichier sécurisé existant</span>
                                        <button
                                            type="button"
                                            wire:click="deleteAttachedFile"
                                            class="text-rose-500 font-bold hover:underline ml-2"
                                        >
                                            Supprimer
                                        </button>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-2">Vous pouvez téléverser un nouveau fichier pour le remplacer.</p>
                                @endif

                                <input
                                    type="file"
                                    wire:model="prescription_file"
                                    accept=".pdf,.jpg,.jpeg,.png"
                                    class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-[#172B4D] file:text-white hover:file:bg-slate-800"
                                >
                                <p class="text-[10px] text-slate-400 mt-1.5">PDF, JPG, PNG jusqu'à 10 Mo. Stockage sécurisé et privé.</p>
                                @error('prescription_file') <span class="text-xs text-rose-600 font-bold block mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button
                            type="button"
                            wire:click="$set('showModal', false)"
                            class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-sm font-bold hover:bg-slate-100 transition"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            class="px-6 py-2.5 rounded-xl bg-[#E82229] hover:bg-[#c91a21] text-white text-sm font-bold shadow-md shadow-[#E82229]/20 transition"
                        >
                            {{ $editingPrescriptionId ? 'Mettre à jour l\'ordonnance' : 'Enregistrer l\'ordonnance' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- DETAIL / VIEW MODAL -->
    @if ($showDetailModal && $viewingPrescription)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 space-y-6">
                <!-- Header -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#172B4D] text-white flex items-center justify-center font-black">
                            OD
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-[#172B4D]">Ordonnance N° #ORD-{{ sprintf('%05d', $viewingPrescription->id) }}</h3>
                            <p class="text-xs text-slate-500">Patient : <strong class="text-[#172B4D]">{{ $viewingPrescription->client->full_name }}</strong></p>
                        </div>
                    </div>
                    <button wire:click="$set('showDetailModal', false)" class="text-slate-400 hover:text-slate-700 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Info summary -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-xs bg-slate-50 p-4 rounded-2xl">
                    <div>
                        <span class="text-slate-400 block font-semibold">Date de prescription</span>
                        <strong class="text-[#172B4D] text-sm">{{ $viewingPrescription->prescription_date->format('d/m/Y') }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Médecin / Prescripteur</span>
                        <strong class="text-[#172B4D] text-sm">{{ $viewingPrescription->prescribed_by ?: 'Non précisé' }}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block font-semibold">Écart Pupillaire Total</span>
                        <strong class="text-[#172B4D] text-sm">{{ $viewingPrescription->pd ? $viewingPrescription->pd . ' mm' : '-' }}</strong>
                    </div>
                </div>

                <!-- Full Refraction Table -->
                <div class="border border-slate-200 rounded-2xl overflow-hidden shadow-xs">
                    <table class="w-full text-center text-xs">
                        <thead class="bg-[#172B4D] text-white uppercase text-[10px] tracking-wider font-bold">
                            <tr>
                                <th class="py-2.5 px-3 text-left">Œil</th>
                                <th class="py-2.5 px-3">Sphère</th>
                                <th class="py-2.5 px-3">Cylindre</th>
                                <th class="py-2.5 px-3">Axe</th>
                                <th class="py-2.5 px-3">Addition</th>
                                <th class="py-2.5 px-3">Demi-EP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-mono font-bold text-slate-800">
                            <tr class="bg-teal-50/40">
                                <td class="py-3 px-3 text-left font-sans text-teal-800 font-black">
                                    <span class="inline-block w-6 py-0.5 text-center bg-teal-600 text-white rounded text-[10px] mr-1.5">OD</span>
                                    Œil Droit
                                </td>
                                <td class="py-3 px-3 text-base">{{ $viewingPrescription->formatValue((string)$viewingPrescription->od_sphere) }}</td>
                                <td class="py-3 px-3 text-base">{{ $viewingPrescription->formatValue((string)$viewingPrescription->od_cylinder) }}</td>
                                <td class="py-3 px-3 text-base">{{ $viewingPrescription->od_axis !== null ? $viewingPrescription->od_axis . '°' : '-' }}</td>
                                <td class="py-3 px-3 text-base text-teal-700">{{ $viewingPrescription->formatValue((string)$viewingPrescription->od_addition) }}</td>
                                <td class="py-3 px-3 text-sm text-slate-600">{{ $viewingPrescription->pd_right ? $viewingPrescription->pd_right . ' mm' : '-' }}</td>
                            </tr>
                            <tr class="bg-sky-50/40">
                                <td class="py-3 px-3 text-left font-sans text-sky-800 font-black">
                                    <span class="inline-block w-6 py-0.5 text-center bg-sky-700 text-white rounded text-[10px] mr-1.5">OG</span>
                                    Œil Gauche
                                </td>
                                <td class="py-3 px-3 text-base">{{ $viewingPrescription->formatValue((string)$viewingPrescription->og_sphere) }}</td>
                                <td class="py-3 px-3 text-base">{{ $viewingPrescription->formatValue((string)$viewingPrescription->og_cylinder) }}</td>
                                <td class="py-3 px-3 text-base">{{ $viewingPrescription->og_axis !== null ? $viewingPrescription->og_axis . '°' : '-' }}</td>
                                <td class="py-3 px-3 text-base text-sky-700">{{ $viewingPrescription->formatValue((string)$viewingPrescription->og_addition) }}</td>
                                <td class="py-3 px-3 text-sm text-slate-600">{{ $viewingPrescription->pd_left ? $viewingPrescription->pd_left . ' mm' : '-' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                @if ($viewingPrescription->notes)
                    <div class="bg-amber-50/70 border border-amber-200/80 rounded-2xl p-4 text-xs">
                        <span class="font-bold text-amber-900 block mb-1">Notes et observations :</span>
                        <p class="text-slate-700 whitespace-pre-line">{{ $viewingPrescription->notes }}</p>
                    </div>
                @endif

                @if ($viewingPrescription->has_file)
                    <div class="flex items-center justify-between p-3.5 bg-teal-50 border border-teal-200 rounded-2xl">
                        <div class="flex items-center gap-2 text-xs font-bold text-[#0F9D91]">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span>Ordonnance papier numérisée disponible</span>
                        </div>
                        <a
                            href="{{ route('prescriptions.file', $viewingPrescription) }}"
                            target="_blank"
                            class="px-4 py-1.5 rounded-xl bg-[#0F9D91] text-white text-xs font-bold hover:bg-teal-700 transition"
                        >
                            Ouvrir le document
                        </a>
                    </div>
                @endif

                <!-- Actions -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        wire:click="$set('showDetailModal', false)"
                        class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-100 transition"
                    >
                        Fermer
                    </button>
                    <button
                        wire:click="openEditModal({{ $viewingPrescription->id }}); $set('showDetailModal', false)"
                        class="px-5 py-2.5 rounded-xl bg-[#172B4D] text-white text-xs font-bold hover:bg-slate-800 transition"
                    >
                        Modifier cette ordonnance
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- DELETE MODAL -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-200 space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-[#E82229] flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-[#172B4D]">Confirmer l'archivage de l'ordonnance</h3>
                    <p class="text-xs text-slate-500 mt-1">
                        Êtes-vous sûr de vouloir supprimer cette ordonnance ? L'historique sera archivé mais restera préservé pour les commandes passées.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-3">
                    <button
                        wire:click="$set('showDeleteModal', false)"
                        class="px-4 py-2 rounded-xl border border-slate-300 text-slate-700 text-xs font-bold hover:bg-slate-100 transition"
                    >
                        Annuler
                    </button>
                    <button
                        wire:click="deletePrescription"
                        class="px-4 py-2 rounded-xl bg-[#E82229] hover:bg-[#c91a21] text-white text-xs font-bold transition shadow-md shadow-[#E82229]/20"
                    >
                        Confirmer la suppression
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

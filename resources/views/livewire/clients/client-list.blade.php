<div>
    <!-- Page Header & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-[#172B4D] tracking-tight">Répertoire des Clients</h2>
            <p class="text-sm text-slate-500 mt-1">Recherche ultra-rapide par nom ou numéro de téléphone, gestion des dossiers et soldes.</p>
        </div>
        <div class="flex items-center gap-3">
            <button
                wire:click="openCreateModal"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-[#E82229] hover:bg-[#D0181F] text-white font-bold text-sm shadow-md shadow-[#E82229]/20 transition"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Nouveau Client</span>
            </button>
        </div>
    </div>

    <!-- Search & Filter Controls -->
    <div class="bg-white border border-slate-200/90 rounded-2xl p-4 sm:p-5 shadow-xs mb-6">
        <div class="flex flex-col sm:flex-row items-center gap-4">
            <!-- Fast Search Input -->
            <div class="relative flex-1 w-full">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.250ms="search"
                    placeholder="Rechercher par nom, prénom ou téléphone (ex: 0661234567)..."
                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:border-transparent transition text-sm font-medium"
                />
                @if ($search)
                    <button
                        wire:click="$set('search', '')"
                        class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Debt Filter Pills -->
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button
                    wire:click="$set('filterDebt', 'all')"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex-1 sm:flex-initial {{ $filterDebt === 'all' ? 'bg-[#172B4D] text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                >
                    Tous les clients
                </button>
                <button
                    wire:click="$set('filterDebt', 'debt_only')"
                    class="px-4 py-2.5 rounded-xl text-xs font-bold transition flex-1 sm:flex-initial {{ $filterDebt === 'debt_only' ? 'bg-red-600 text-white shadow-xs' : 'bg-red-50 text-[#E82229] hover:bg-red-100' }}"
                >
                    Soldes à régler uniquement
                </button>
            </div>
        </div>
    </div>

    <!-- Clients Table -->
    <div class="bg-white border border-slate-200/90 rounded-2xl shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 text-xs font-bold uppercase tracking-wider border-b border-slate-200/80">
                        <th class="py-4 px-4 sm:px-6">Patient / Client</th>
                        <th class="py-4 px-4">Téléphone</th>
                        <th class="py-4 px-4">Ville</th>
                        <th class="py-4 px-4">Dernière Commande</th>
                        <th class="py-4 px-4 text-center">Total Commandes</th>
                        <th class="py-4 px-4">Solde Restant</th>
                        <th class="py-4 px-4 sm:px-6 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($clients as $client)
                        @php
                            $debt = $client->totalOutstandingDebt();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition">
                            <!-- Client Name & Info -->
                            <td class="py-4 px-4 sm:px-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#172B4D]/10 text-[#172B4D] border border-[#172B4D]/15 flex items-center justify-center font-bold text-sm flex-shrink-0">
                                        {{ strtoupper(substr($client->first_name, 0, 1) . substr($client->last_name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('clients.show', $client) }}" class="font-bold text-[#172B4D] hover:text-[#E82229] transition block">
                                            {{ $client->full_name }}
                                        </a>
                                        <div class="flex items-center gap-2 text-xs text-slate-500 mt-0.5">
                                            @if ($client->gender)
                                                <span>{{ $client->gender === 'male' ? 'Homme' : ($client->gender === 'female' ? 'Femme' : 'Enfant') }}</span>
                                            @endif
                                            @if ($client->age)
                                                <span>• {{ $client->age }} ans</span>
                                            @endif
                                            @if ($client->email)
                                                <span class="truncate max-w-[150px]">• {{ $client->email }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Phone with Call link -->
                            <td class="py-4 px-4">
                                <a href="tel:{{ $client->phone }}" class="inline-flex items-center gap-1.5 font-semibold text-[#172B4D] hover:text-[#0F9D91] transition text-sm">
                                    <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                                    </svg>
                                    <span>{{ $client->phone }}</span>
                                </a>
                            </td>

                            <!-- City -->
                            <td class="py-4 px-4 text-slate-600 font-medium">
                                {{ $client->city ?? 'Marrakech' }}
                            </td>

                            <!-- Latest Order -->
                            <td class="py-4 px-4">
                                @if ($client->latestOrder)
                                    <div>
                                        <span class="font-mono text-xs font-bold text-[#172B4D]">{{ $client->latestOrder->order_number }}</span>
                                        <div class="text-xs text-slate-400">{{ $client->latestOrder->order_date->format('d/m/Y') }}</div>
                                    </div>
                                @else
                                    <span class="text-xs text-slate-400 italic">Aucune</span>
                                @endif
                            </td>

                            <!-- Orders Count -->
                            <td class="py-4 px-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $client->orders_count }}
                                </span>
                            </td>

                            <!-- Remaining Balance -->
                            <td class="py-4 px-4">
                                @if ($debt > 0)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-extrabold bg-red-50 text-[#E82229] border border-red-200">
                                        {{ number_format($debt, 2, ',', ' ') }} {{ $storeSetting->currency }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold bg-teal-50 text-[#0F9D91] border border-teal-200">
                                        0,00 {{ $storeSetting->currency }} (À jour)
                                    </span>
                                @endif
                            </td>

                            <!-- Action Buttons -->
                            <td class="py-4 px-4 sm:px-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a
                                        href="{{ route('clients.show', $client) }}"
                                        class="p-2 text-slate-600 hover:text-[#172B4D] hover:bg-slate-100 rounded-lg transition"
                                        title="Voir la fiche complète"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>

                                    <button
                                        wire:click="openEditModal({{ $client->id }})"
                                        class="p-2 text-slate-600 hover:text-indigo-600 hover:bg-slate-100 rounded-lg transition"
                                        title="Modifier les coordonnées"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <button
                                        wire:click="confirmDelete({{ $client->id }})"
                                        class="p-2 text-slate-400 hover:text-[#E82229] hover:bg-red-50 rounded-lg transition"
                                        title="Supprimer la fiche"
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
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-3">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                </div>
                                <p class="font-semibold text-slate-600">Aucun client trouvé.</p>
                                <p class="text-xs text-slate-400 mt-1">Essayez un autre mot-clé de recherche ou créez un nouveau client.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($clients->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $clients->links() }}
            </div>
        @endif
    </div>

    <!-- Create / Edit Client Modal -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs overflow-y-auto">
            <div class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-slate-200 my-8">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-[#172B4D] text-white flex items-center justify-center font-bold">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-[#172B4D]">
                                {{ $editingClientId ? 'Modifier la Fiche Client' : 'Nouveau Patient / Client' }}
                            </h3>
                            <p class="text-xs text-slate-500">Coordonnées et informations générales du patient.</p>
                        </div>
                    </div>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600 p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form wire:submit="saveClient" class="space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Prénom <span class="text-[#E82229]">*</span></label>
                            <input type="text" wire:model="first_name" placeholder="Ex: Amine" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm font-medium">
                            @error('first_name') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Nom <span class="text-[#E82229]">*</span></label>
                            <input type="text" wire:model="last_name" placeholder="Ex: El Mansouri" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm font-medium">
                            @error('last_name') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Téléphone <span class="text-[#E82229]">*</span></label>
                            <input type="text" wire:model="phone" placeholder="Ex: 06 61 23 45 67" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm font-semibold">
                            @error('phone') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Email</label>
                            <input type="email" wire:model="email" placeholder="patient@example.ma" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm">
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
                            <input type="text" wire:model="address" placeholder="Ex: Guéliz, Résidence Al Badii Apt 4" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm">
                            @error('address') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Ville</label>
                            <input type="text" wire:model="city" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm font-medium">
                            @error('city') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1">Notes & Observations particulières</label>
                            <textarea wire:model="notes" rows="3" placeholder="Préférences montures, antécédents visuels, mutuelle..." class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-slate-900 focus:bg-white focus:ring-2 focus:ring-[#172B4D] focus:outline-none text-sm"></textarea>
                            @error('notes') <span class="text-xs text-[#E82229] mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <button
                            type="button"
                            wire:click="$set('showModal', false)"
                            class="px-5 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-semibold text-sm transition"
                        >
                            Annuler
                        </button>
                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            class="px-6 py-2.5 rounded-xl bg-[#E82229] hover:bg-[#D0181F] text-white font-bold text-sm shadow-md shadow-[#E82229]/20 transition disabled:opacity-50 flex items-center gap-2"
                        >
                            <span wire:loading.remove>{{ $editingClientId ? 'Mettre à jour' : 'Enregistrer le client' }}</span>
                            <span wire:loading>Enregistrement...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Delete Confirmation Modal -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs">
            <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-200">
                <div class="w-12 h-12 rounded-2xl bg-red-100 text-[#E82229] flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h3 class="text-xl font-black text-[#172B4D]">Confirmer la suppression</h3>
                <p class="text-sm text-slate-600 mt-2">
                    Êtes-vous certain de vouloir supprimer cette fiche client ?
                </p>

                @if ($deleteWarning)
                    <div class="mt-4 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium">
                        {{ $deleteWarning }}
                    </div>
                @endif

                <div class="flex items-center justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                    <button
                        wire:click="$set('showDeleteModal', false)"
                        class="px-4 py-2.5 rounded-xl border border-slate-300 text-slate-700 hover:bg-slate-100 font-semibold text-sm transition"
                    >
                        Annuler
                    </button>
                    <button
                        wire:click="deleteClient"
                        class="px-5 py-2.5 rounded-xl bg-[#E82229] hover:bg-[#D0181F] text-white font-bold text-sm shadow-md shadow-[#E82229]/20 transition"
                    >
                        Supprimer définitivement
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

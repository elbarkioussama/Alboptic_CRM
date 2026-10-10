<div class="max-w-5xl mx-auto space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h2 class="text-2xl sm:text-3xl font-black text-[#172B4D] tracking-tight">Paramètres du magasin</h2>
            <p class="text-sm text-slate-500 mt-1">Configurez les coordonnées légales, les identifiants fiscaux et la numérotation des documents.</p>
        </div>
        <div>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-[#172B4D]/10 text-[#172B4D] border border-[#172B4D]/20">
                Accès Administrateur
            </span>
        </div>
    </div>

    <!-- Form Body -->
    <form wire:submit="save" class="space-y-8">
        <!-- Section 1: Identité du Magasin -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-[#172B4D] text-white flex items-center justify-center font-bold text-sm">1</div>
                <div>
                    <h3 class="text-lg font-bold text-[#172B4D]">Identité & Coordonnées de l'Établissement</h3>
                    <p class="text-xs text-slate-500">Ces informations figureront sur vos devis, bons de commande et factures.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Raison sociale (Nom officiel)</label>
                    <input type="text" wire:model="business_name" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-[#172B4D] focus:border-transparent focus:outline-none text-sm shadow-xs">
                    @error('business_name') <span class="text-xs text-[#E82229] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Nom commercial / Enseigne</label>
                    <input type="text" wire:model="trade_name" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-[#172B4D] focus:border-transparent focus:outline-none text-sm shadow-xs">
                    @error('trade_name') <span class="text-xs text-[#E82229] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Adresse complète</label>
                    <input type="text" wire:model="address" placeholder="Ex: Avenue Mohammed V, Guéliz" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-[#172B4D] focus:border-transparent focus:outline-none text-sm shadow-xs">
                    @error('address') <span class="text-xs text-[#E82229] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Ville</label>
                    <input type="text" wire:model="city" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-[#172B4D] focus:border-transparent focus:outline-none text-sm shadow-xs">
                    @error('city') <span class="text-xs text-[#E82229] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Téléphone de contact</label>
                    <input type="text" wire:model="phone" placeholder="+212 5 24 ..." class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-[#172B4D] focus:border-transparent focus:outline-none text-sm shadow-xs">
                    @error('phone') <span class="text-xs text-[#E82229] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Email officiel</label>
                    <input type="email" wire:model="email" placeholder="contact@alboptic.ma" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-[#172B4D] focus:border-transparent focus:outline-none text-sm shadow-xs">
                    @error('email') <span class="text-xs text-[#E82229] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Section 2: Données Légales & Fiscales (Maroc) -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-[#0F9D91] text-white flex items-center justify-center font-bold text-sm">2</div>
                <div>
                    <h3 class="text-lg font-bold text-[#172B4D]">Mentions Légales & Fiscales (Normes Marocaines)</h3>
                    <p class="text-xs text-slate-500">Identifiants obligatoires selon la législation commerciale et fiscale au Maroc.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">ICE (15 chiffres)</label>
                    <input type="text" wire:model="ice" placeholder="001928374650001" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-[#172B4D] focus:border-transparent focus:outline-none text-sm font-mono shadow-xs">
                    @error('ice') <span class="text-xs text-[#E82229] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">RC (Registre du Commerce)</label>
                    <input type="text" wire:model="rc" placeholder="Ex: 48291 Marrakech" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-[#172B4D] focus:border-transparent focus:outline-none text-sm shadow-xs">
                    @error('rc') <span class="text-xs text-[#E82229] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Identifiant Fiscal (IF)</label>
                    <input type="text" wire:model="tax_identifier" placeholder="Ex: 39201948" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-[#172B4D] focus:border-transparent focus:outline-none text-sm shadow-xs">
                    @error('tax_identifier') <span class="text-xs text-[#E82229] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Section 3: Facturation & Numérotation -->
        <div class="bg-white border border-slate-200/90 rounded-2xl p-6 sm:p-8 shadow-xs space-y-6">
            <div class="flex items-center gap-3 pb-4 border-b border-slate-100">
                <div class="w-8 h-8 rounded-lg bg-[#172B4D] text-white flex items-center justify-center font-bold text-sm">3</div>
                <div>
                    <h3 class="text-lg font-bold text-[#172B4D]">Facturation, TVA & Numérotation</h3>
                    <p class="text-xs text-slate-500">Préfixes de numérotation séquentielle et taux de TVA par défaut.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Préfixe Bons de Commande</label>
                    <input type="text" wire:model="order_prefix" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-[#172B4D] focus:border-transparent focus:outline-none text-sm font-mono shadow-xs">
                    <span class="text-[11px] text-slate-400 mt-1 block">Exemple: BC-2026-000001</span>
                    @error('order_prefix') <span class="text-xs text-[#E82229] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Préfixe Factures</label>
                    <input type="text" wire:model="invoice_prefix" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-[#172B4D] focus:border-transparent focus:outline-none text-sm font-mono shadow-xs">
                    <span class="text-[11px] text-slate-400 mt-1 block">Exemple: FAC-2026-000001</span>
                    @error('invoice_prefix') <span class="text-xs text-[#E82229] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Taux TVA par défaut (%)</label>
                    <input type="number" step="0.5" wire:model="default_tax_rate" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-[#172B4D] focus:border-transparent focus:outline-none text-sm shadow-xs">
                    <span class="text-[11px] text-slate-400 mt-1 block">Taux légal: 20%</span>
                    @error('default_tax_rate') <span class="text-xs text-[#E82229] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-1">Devise principale</label>
                    <input type="text" wire:model="currency" class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-xl text-slate-900 focus:ring-2 focus:ring-[#172B4D] focus:border-transparent focus:outline-none text-sm font-bold shadow-xs">
                    <span class="text-[11px] text-slate-400 mt-1 block">Dirham Marocain (MAD)</span>
                    @error('currency') <span class="text-xs text-[#E82229] mt-1 block font-medium">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Submit Button: Red #E82229 (Primary Button) -->
        <div class="flex items-center justify-end gap-4 pt-4">
            <button
                type="submit"
                wire:loading.attr="disabled"
                class="px-8 py-3.5 rounded-xl bg-[#E82229] hover:bg-[#D0181F] text-white font-bold text-sm shadow-lg shadow-[#E82229]/25 transition disabled:opacity-50 flex items-center gap-2"
            >
                <svg wire:loading.remove class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span wire:loading.remove>Enregistrer les modifications</span>
                <span wire:loading>Enregistrement en cours...</span>
            </button>
        </div>
    </form>
</div>

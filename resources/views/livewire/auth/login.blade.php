<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8 bg-slate-100">
    <div class="max-w-md w-full space-y-8 bg-white p-8 sm:p-10 rounded-3xl shadow-xl border border-slate-200">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-[#172B4D] text-white shadow-lg shadow-[#172B4D]/20 mb-4">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </div>
            <h2 class="text-3xl font-black text-[#172B4D] tracking-tight">ALBOPTIC CRM</h2>
            <p class="mt-1 text-sm text-slate-500">Magasin d'optique & lunetterie — Marrakech</p>
        </div>

        <form wire:submit="login" class="mt-8 space-y-6">
            @if ($errors->any())
                <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-[#E82229] text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Adresse e-mail</label>
                    <input
                        wire:model="email"
                        id="email"
                        type="email"
                        required
                        autocomplete="email"
                        placeholder="admin@alboptic.ma"
                        class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172B4D] focus:border-transparent transition text-sm shadow-xs"
                    />
                </div>

                <div>
                    <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Mot de passe</label>
                    <input
                        wire:model="password"
                        id="password"
                        type="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full px-4 py-3 bg-white border border-slate-300 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#172B4D] focus:border-transparent transition text-sm shadow-xs"
                    />
                </div>
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input
                        wire:model="remember"
                        id="remember"
                        type="checkbox"
                        class="h-4 w-4 rounded border-slate-300 text-[#E82229] focus:ring-[#E82229]"
                    />
                    <label for="remember" class="ml-2 block text-sm text-slate-600">Se souvenir de moi</label>
                </div>
            </div>

            <div>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-lg shadow-[#E82229]/25 text-sm font-bold text-white bg-[#E82229] hover:bg-[#D0181F] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#E82229] transition disabled:opacity-50"
                >
                    <span wire:loading.remove>Se connecter au CRM</span>
                    <span wire:loading>Connexion en cours...</span>
                </button>
            </div>

            <div class="text-center text-xs text-slate-400 pt-2 border-t border-slate-100">
                <p>Identifiants de démonstration :</p>
                <p class="font-mono text-slate-600 mt-1 font-semibold">admin@alboptic.ma / password</p>
            </div>
        </form>
    </div>
</div>

<x-layouts.app :title="$title ?? 'Module en cours'">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl p-12 text-center shadow-xl">
        <div class="w-16 h-16 rounded-2xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center mx-auto mb-4">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-white">{{ $title ?? 'Module' }}</h2>
        <p class="text-slate-400 mt-2 max-w-md mx-auto">{{ $description ?? 'Ce module sera développé dans la phase suivante conformément à la feuille de route du projet.' }}</p>
        <div class="mt-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-sm font-semibold transition">
                &larr; Retour au Tableau de bord
            </a>
        </div>
    </div>
</x-layouts.app>

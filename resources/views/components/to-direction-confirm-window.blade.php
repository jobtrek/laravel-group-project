<div
    x-show="{{ $show }}"
    x-init="$watch('{{ $show }}', value => {
        value
            ? document.body.classList.add('overflow-y-hidden')
            : document.body.classList.remove('overflow-y-hidden');
    })"
    x-transition:enter="ease-out duration-200"
    x-transition:enter-start="opacity-0 scale-95"
    x-transition:enter-end="opacity-100 scale-100"
    x-transition:leave="ease-in duration-150"
    x-transition:leave-start="opacity-100 scale-100"
    x-transition:leave-end="opacity-0 scale-95"
    @click.outside="open = false"
    class="fixed inset-0 z-50 flex items-start justify-center pt-24 px-4 pointer-events-none"
>
    <div
        class="w-full max-w-md rounded-xl bg-white p-6 shadow-xl pointer-events-auto"
    >
        <div class="flex items-start gap-4">
            <div class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full bg-blue-100">
                <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-slate-900">Soumettre la proposition ?</h3>
                <p class="mt-1 text-sm text-slate-500">
                    Afin de garantir une évaluation précise et réaliste de votre projet, assurez-vous d'avoir passé en revue les différents points de manière claire et adéquate.
                </p>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <x-projects.buttons
                @click="open = false"
                text="Annuler"
                class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50"
                type="button"
            />
            <form action="{{ $route }}" method="POST">
                @csrf
                @method('PATCH')
                <x-projects.buttons
                    text="Envoyer"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                    type="submit"
                />
            </form>
        </div>
    </div>
</div>

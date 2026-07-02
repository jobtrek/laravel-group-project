<div x-show="{{ $show }}">
    <h3>Soumettre la proposition ?</h3>
    <p>Afin de garantir une évaluation précise et réaliste de votre projets, assurez-vous d'avoir passer en revue les différents points de votre projets.</p>
    <form action="{{ $route }}" method="POST" class="relative z-10">
        @csrf
        @method('PATCH')
        <x-projects.buttons
            text="Envoyer"
            class="bg-blue-700 text-white p-2"
            type="submit"
        />
    </form>
    <x-projects.buttons
        @click="open = false"
        text="Annuler"
        class="bg-blue-700 text-white p-2"
        type="button"
    />
</div>

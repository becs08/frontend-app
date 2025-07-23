<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer une nouvelle offre') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if ($errors->any())
                    <div class="mb-4 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form method="POST" action="{{ route('offres.store') }}" class="space-y-6">
                        @csrf

                        <!-- Titre -->
                        <div>
                            <x-input-label for="titre" :value="__('Titre de l\'offre')" />
                            <x-text-input
                                id="titre"
                                class="block mt-1 w-full"
                                type="text"
                                name="titre"
                                :value="old('titre')"
                                required
                                autofocus
                                placeholder="Ex: Cours de piano à domicile" />
                            <x-input-error :messages="$errors->get('titre')" class="mt-2" />
                        </div>

                        <!-- Description -->
                        <div>
                            <x-input-label for="description" :value="__('Description')" />
                            <textarea
                                id="description"
                                name="description"
                                rows="5"
                                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                required
                                placeholder="Décrivez votre offre en détail...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Catégorie -->
                            <div>
                                <x-input-label for="categorie" :value="__('Catégorie')" />
                                <select name="categorie" id="categorie" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Choisir une catégorie</option>
                                    @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('categorie') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                                <x-input-error :messages="$errors->get('categorie')" class="mt-2" />
                            </div>

                            <!-- Type d'offre -->
                            <div>
                                <x-input-label for="type_offre" :value="__('Type d\'offre')" />
                                <select name="type_offre" id="type_offre" class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="">Choisir un type</option>
                                    <option value="service" {{ old('type_offre') === 'service' ? 'selected' : '' }}>Service</option>
                                    <option value="produit" {{ old('type_offre') === 'produit' ? 'selected' : '' }}>Produit</option>
                                    <option value="formation" {{ old('type_offre') === 'formation' ? 'selected' : '' }}>Formation</option>
                                </select>
                                <x-input-error :messages="$errors->get('type_offre')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Localisation -->
                        <div>
                            <x-input-label for="localisation" :value="__('Localisation')" />
                            <x-text-input
                                id="localisation"
                                class="block mt-1 w-full"
                                type="text"
                                name="localisation"
                                :value="old('localisation')"
                                required
                                placeholder="Ex: Paris 15ème, Lyon, etc." />
                            <x-input-error :messages="$errors->get('localisation')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Prix minimum -->
                            <div>
                                <x-input-label for="prix_min" :value="__('Prix minimum (€)')" />
                                <x-text-input
                                    id="prix_min"
                                    class="block mt-1 w-full"
                                    type="number"
                                    name="prix_min"
                                    :value="old('prix_min')"
                                    min="0"
                                    step="0.01"
                                    placeholder="Optionnel" />
                                <x-input-error :messages="$errors->get('prix_min')" class="mt-2" />
                            </div>

                            <!-- Prix maximum -->
                            <div>
                                <x-input-label for="prix_max" :value="__('Prix maximum (€)')" />
                                <x-text-input
                                    id="prix_max"
                                    class="block mt-1 w-full"
                                    type="number"
                                    name="prix_max"
                                    :value="old('prix_max')"
                                    min="0"
                                    step="0.01"
                                    placeholder="Optionnel" />
                                <x-input-error :messages="$errors->get('prix_max')" class="mt-2" />
                            </div>
                        </div>

                        <!-- Date d'expiration -->
                        <div>
                            <x-input-label for="date_expiration" :value="__('Date d\'expiration (optionnel)')" />
                            <x-text-input
                                id="date_expiration"
                                class="block mt-1 w-full"
                                type="date"
                                name="date_expiration"
                                :value="old('date_expiration')"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}" />
                            <x-input-error :messages="$errors->get('date_expiration')" class="mt-2" />
                            <p class="text-sm text-gray-600 mt-1">Si non renseignée, l'offre restera active indéfiniment</p>
                        </div>

                        <!-- Boutons -->
                        <div class="flex items-center justify-end mt-6 space-x-3">
                            <a href="{{ route('offres.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Annuler
                            </a>
                            <x-primary-button class="ml-4">
                                {{ __('Créer l\'offre') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

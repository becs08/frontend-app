<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Offres disponibles') }}
            </h2>
            @if(Session::has('api_token'))
            <a href="{{ route('offres.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Publier une offre
            </a>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Filtres -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 bg-gray-50">
                    <form method="GET" action="{{ route('offres.index') }}" class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <label for="search" class="block text-sm font-medium text-gray-700">Recherche</label>
                                <input type="text" name="search" id="search" value="{{ $filters['search'] ?? '' }}"
                                       placeholder="Titre, description..."
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="categorie" class="block text-sm font-medium text-gray-700">Catégorie</label>
                                <select name="categorie" id="categorie" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Toutes les catégories</option>
                                    @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ ($filters['categorie'] ?? '') === $key ? 'selected' : '' }}>
                                    {{ $label }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label for="localisation" class="block text-sm font-medium text-gray-700">Localisation</label>
                                <input type="text" name="localisation" id="localisation" value="{{ $filters['localisation'] ?? '' }}"
                                       placeholder="Ville, région..."
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <div>
                                <label for="type_offre" class="block text-sm font-medium text-gray-700">Type</label>
                                <select name="type_offre" id="type_offre" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Tous les types</option>
                                    <option value="service" {{ ($filters['type_offre'] ?? '') === 'service' ? 'selected' : '' }}>Service</option>
                                    <option value="produit" {{ ($filters['type_offre'] ?? '') === 'produit' ? 'selected' : '' }}>Produit</option>
                                    <option value="formation" {{ ($filters['type_offre'] ?? '') === 'formation' ? 'selected' : '' }}>Formation</option>
                                </select>
                            </div>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('offres.index') }}" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                                Réinitialiser
                            </a>
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Résultats -->
            @if(isset($offres['data']) && count($offres['data']) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($offres['data'] as $offre)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($offre['type_offre']) }}
                                    </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        {{ ucfirst($offre['categorie']) }}
                                    </span>
                        </div>

                        <h3 class="text-lg font-semibold mb-2">
                            <a href="{{ route('offres.show', $offre['id']) }}" class="text-blue-600 hover:text-blue-800">
                                {{ $offre['titre'] }}
                            </a>
                        </h3>

                        <p class="text-gray-600 text-sm mb-3">
                            {{ \Str::limit($offre['description'], 100) }}
                        </p>

                        <div class="flex justify-between items-center text-sm text-gray-500 mb-3">
                            <span>📍 {{ $offre['localisation'] }}</span>
                            <span>💰 {{ $offre['prix_formate'] }}</span>
                        </div>

                        <div class="flex justify-between items-center text-sm">
                                    <span class="text-gray-500">
                                        Par {{ $offre['author_name'] }}
                                    </span>
                            <span class="text-gray-400">
                                        {{ \Carbon\Carbon::parse($offre['created_at'])->diffForHumans() }}
                                    </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination simple -->
            @if($offres['last_page'] > 1)
            <div class="flex justify-center space-x-2">
                @if($offres['current_page'] > 1)
                <a href="{{ route('offres.index', array_merge($filters, ['page' => $offres['current_page'] - 1])) }}"
                   class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Précédent
                </a>
                @endif

                <span class="px-3 py-2 bg-blue-500 text-white rounded">
                            Page {{ $offres['current_page'] }} sur {{ $offres['last_page'] }}
                        </span>

                @if($offres['current_page'] < $offres['last_page'])
                <a href="{{ route('offres.index', array_merge($filters, ['page' => $offres['current_page'] + 1])) }}"
                   class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Suivant
                </a>
                @endif
            </div>
            @endif
            @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-center text-gray-500">
                    <p class="text-lg">Aucune offre trouvée.</p>
                    <p class="text-sm mt-2">Essayez de modifier vos critères de recherche.</p>
                    @if(Session::has('api_token'))
                    <a href="{{ route('offres.create') }}" class="inline-block mt-4 bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Publier la première offre
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

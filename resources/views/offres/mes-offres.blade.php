<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Mes offres') }}
            </h2>
            <a href="{{ route('offres.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Créer une nouvelle offre
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if(isset($offres['data']) && count($offres['data']) > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                @foreach($offres['data'] as $offre)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                    <div class="p-6">
                        <!-- En-tête avec statut -->
                        <div class="flex justify-between items-start mb-3">
                            <div class="flex space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ ucfirst($offre['type_offre']) }}
                                        </span>
                                @if($offre['statut'] === 'active')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Active
                                            </span>
                                @elseif($offre['statut'] === 'suspendue')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Suspendue
                                            </span>
                                @elseif($offre['statut'] === 'expiree')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Expirée
                                            </span>
                                @endif
                            </div>
                            <div class="text-right">
                                @if(isset($offre['demandes_count']))
                                <div class="text-sm text-gray-600">
                                    {{ $offre['demandes_count'] }} demande(s)
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Titre -->
                        <h3 class="font-semibold text-lg text-gray-900 mb-2">
                            <a href="{{ route('offres.show', $offre['id']) }}" class="hover:text-blue-600">
                                {{ $offre['titre'] }}
                            </a>
                        </h3>

                        <!-- Description -->
                        <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                            {{ Str::limit($offre['description'], 120) }}
                        </p>

                        <!-- Informations -->
                        <div class="flex justify-between items-center text-xs text-gray-500 mb-4">
                            <span>📍 {{ $offre['localisation'] }}</span>
                            <span>{{ isset($offre['prix_formate']) ? $offre['prix_formate'] : 'Prix à négocier' }}</span>
                        </div>

                        <!-- Dates -->
                        <div class="text-xs text-gray-500 mb-4">
                            <div>Créée le {{ \Carbon\Carbon::parse($offre['created_at'])->format('d/m/Y') }}</div>
                            @if($offre['date_expiration'])
                            <div>Expire le {{ \Carbon\Carbon::parse($offre['date_expiration'])->format('d/m/Y') }}</div>
                            @endif
                            <div>{{ $offre['vues'] ?? 0 }} vue(s)</div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between items-center">
                            <a href="{{ route('offres.show', $offre['id']) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                Voir le détail
                            </a>
                            <div class="flex space-x-2">
                                <a href="{{ route('offres.edit', $offre['id']) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white text-xs font-bold py-1 px-2 rounded">
                                    Modifier
                                </a>
                                <form method="POST" action="{{ route('offres.destroy', $offre['id']) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white text-xs font-bold py-1 px-2 rounded">
                                        Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if(isset($offres['last_page']) && $offres['last_page'] > 1)
            <div class="flex justify-center space-x-2">
                @if($offres['current_page'] > 1)
                <a href="{{ route('offres.mes-offres', ['page' => $offres['current_page'] - 1]) }}"
                   class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Précédent
                </a>
                @endif

                <span class="px-3 py-2 bg-blue-500 text-white rounded">
                            Page {{ $offres['current_page'] }} sur {{ $offres['last_page'] }}
                        </span>

                @if($offres['current_page'] < $offres['last_page'])
                <a href="{{ route('offres.mes-offres', ['page' => $offres['current_page'] + 1]) }}"
                   class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Suivant
                </a>
                @endif
            </div>
            @endif
            @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-8 text-center text-gray-500">
                    <div class="mb-4">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune offre créée</h3>
                    <p class="text-gray-600 mb-4">Vous n'avez pas encore créé d'offre. Commencez dès maintenant !</p>
                    <a href="{{ route('offres.create') }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Créer ma première offre
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

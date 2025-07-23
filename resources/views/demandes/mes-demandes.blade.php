<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mes demandes envoyées') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if(isset($demandes['data']) && count($demandes['data']) > 0)
            <div class="space-y-6">
                @foreach($demandes['data'] as $demande)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <!-- Titre de l'offre -->
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                    <a href="{{ route('offres.show', $demande['offre']['id']) }}" class="hover:text-blue-600">
                                        {{ $demande['offre']['titre'] ?? 'Titre non disponible' }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Proposé par {{ $demande['offre']['user']['name'] ?? 'Utilisateur' }}
                                </p>
                            </div>

                            <!-- Statut -->
                            <div class="flex items-center space-x-2">
                                @if($demande['statut'] === 'en_attente')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                En attente
                                            </span>
                                @elseif($demande['statut'] === 'acceptee')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                Acceptée
                                            </span>
                                @elseif($demande['statut'] === 'refusee')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                Refusée
                                            </span>
                                @elseif($demande['statut'] === 'annulee')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                Annulée
                                            </span>
                                @endif
                            </div>
                        </div>

                        <!-- Mon message -->
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-900 mb-2">Mon message :</h4>
                            <div class="bg-blue-50 p-3 rounded-lg">
                                <p class="text-gray-700 whitespace-pre-line">{{ $demande['message'] ?? 'Message non disponible' }}</p>
                                @if(isset($demande['prix_propose']) && $demande['prix_propose'])
                                <p class="text-green-600 font-medium mt-2">
                                    💰 Prix proposé : {{ number_format($demande['prix_propose'], 2) }}€
                                </p>
                                @endif
                            </div>
                        </div>

                        <!-- Réponse du prestataire -->
                        @if(isset($demande['message_reponse']) && $demande['message_reponse'])
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-900 mb-2">Réponse du prestataire :</h4>
                            <div class="bg-gray-50 p-3 rounded-lg">
                                <p class="text-gray-700 whitespace-pre-line">{{ $demande['message_reponse'] }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Informations sur la demande -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 border-t pt-4">
                            <div>
                                <strong>📅 Demande envoyée le :</strong><br>
                                {{ isset($demande['created_at']) ? \Carbon\Carbon::parse($demande['created_at'])->format('d/m/Y à H:i') : 'Date non disponible' }}
                            </div>
                            @if(isset($demande['date_reponse']) && $demande['date_reponse'])
                            <div>
                                <strong>📝 Réponse reçue le :</strong><br>
                                {{ \Carbon\Carbon::parse($demande['date_reponse'])->format('d/m/Y à H:i') }}
                            </div>
                            @endif
                            <div>
                                <strong>📍 Localisation :</strong><br>
                                {{ $demande['offre']['localisation'] ?? 'Non spécifiée' }}
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between items-center mt-4 pt-4 border-t">
                            <a href="{{ route('offres.show', $demande['offre']['id']) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                Voir l'offre complète →
                            </a>

                            @if($demande['statut'] === 'acceptee')
                            <div class="flex items-center text-green-600">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Demande acceptée ! Vous pouvez contacter le prestataire.
                            </div>
                            @elseif($demande['statut'] === 'en_attente')
                            <div class="text-yellow-600">
                                ⏳ En attente de réponse...
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            @if(isset($demandes['last_page']) && $demandes['last_page'] > 1)
            <div class="mt-8 flex justify-center space-x-2">
                @if($demandes['current_page'] > 1)
                <a href="{{ route('demandes.mes-demandes', ['page' => $demandes['current_page'] - 1]) }}"
                   class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Précédent
                </a>
                @endif

                <span class="px-3 py-2 bg-blue-500 text-white rounded">
                            Page {{ $demandes['current_page'] }} sur {{ $demandes['last_page'] }}
                        </span>

                @if($demandes['current_page'] < $demandes['last_page'])
                <a href="{{ route('demandes.mes-demandes', ['page' => $demandes['current_page'] + 1]) }}"
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-4.906-1.456L3 21l2.544-5.094A8.959 8.959 0 013 12c0-4.418 3.582-8 8-8s8 3.582 8 8z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune demande envoyée</h3>
                    <p class="text-gray-600 mb-4">Vous n'avez pas encore envoyé de demande. Parcourez les offres pour trouver ce qui vous intéresse !</p>
                    <a href="{{ route('offres.index') }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                        Parcourir les offres
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

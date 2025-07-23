<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Demandes reçues sur mes offres') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
            @endif

            @if ($errors->any())
            <div class="mb-6 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(isset($demandes['data']) && count($demandes['data']) > 0)
            <div class="space-y-6">
                @foreach($demandes['data'] as $demande)
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <!-- Informations sur l'offre -->
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">
                                    Demande pour :
                                    <a href="{{ route('offres.show', $demande['offre']['id']) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $demande['offre']['titre'] }}
                                    </a>
                                </h3>
                                <p class="text-sm text-gray-600">
                                    Demandeur : <strong>{{ $demande['demandeur']['name'] }}</strong>
                                </p>
                            </div>

                            <!-- Statut -->
                            <div class="flex items-center space-x-2">
                                @if($demande['statut'] === 'en_attente')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-yellow-100 text-yellow-800">
                                                ⏳ En attente
                                            </span>
                                @elseif($demande['statut'] === 'acceptee')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                                ✅ Acceptée
                                            </span>
                                @elseif($demande['statut'] === 'refusee')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                                ❌ Refusée
                                            </span>
                                @endif
                            </div>
                        </div>

                        <!-- Message du demandeur -->
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-900 mb-2">Message du demandeur :</h4>
                            <div class="bg-blue-50 p-4 rounded-lg">
                                <p class="text-gray-700 whitespace-pre-line">{{ $demande['message'] }}</p>
                                @if($demande['prix_propose'])
                                <div class="mt-3 p-2 bg-green-100 rounded">
                                    <p class="text-green-700 font-medium">
                                        💰 Prix proposé : {{ number_format($demande['prix_propose'], 2) }}€
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Ma réponse (si déjà répondu) -->
                        @if($demande['message_reponse'])
                        <div class="mb-4">
                            <h4 class="font-medium text-gray-900 mb-2">Ma réponse :</h4>
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <p class="text-gray-700 whitespace-pre-line">{{ $demande['message_reponse'] }}</p>
                            </div>
                        </div>
                        @endif

                        <!-- Formulaire de réponse (si pas encore répondu) -->
                        @if($demande['statut'] === 'en_attente')
                        <div class="border-t pt-4">
                            <h4 class="font-medium text-gray-900 mb-3">Répondre à cette demande :</h4>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Accepter -->
                                <div class="border border-green-200 rounded-lg p-4 bg-green-50">
                                    <h5 class="font-medium text-green-800 mb-2">✅ Accepter la demande</h5>
                                    <form method="POST" action="{{ route('demandes.update', $demande['id']) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="statut" value="acceptee">

                                        <div class="mb-3">
                                                        <textarea
                                                            name="message_reponse"
                                                            rows="3"
                                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                                                            placeholder="Message d'acceptation (optionnel)..."></textarea>
                                        </div>

                                        <button type="submit" class="w-full bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                                            Accepter la demande
                                        </button>
                                    </form>
                                </div>

                                <!-- Refuser -->
                                <div class="border border-red-200 rounded-lg p-4 bg-red-50">
                                    <h5 class="font-medium text-red-800 mb-2">❌ Refuser la demande</h5>
                                    <form method="POST" action="{{ route('demandes.update', $demande['id']) }}">
                                        @csrf
                                        @method('PUT')
                                        <input type="hidden" name="statut" value="refusee">

                                        <div class="mb-3">
                                                        <textarea
                                                            name="message_reponse"
                                                            rows="3"
                                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"
                                                            placeholder="Raison du refus (optionnel)..."></textarea>
                                        </div>

                                        <button type="submit" class="w-full bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                            Refuser la demande
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Informations sur la demande -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600 border-t pt-4 mt-4">
                            <div>
                                <strong>📅 Demande reçue le :</strong><br>
                                {{ \Carbon\Carbon::parse($demande['created_at'])->format('d/m/Y à H:i') }}
                            </div>
                            @if($demande['date_reponse'])
                            <div>
                                <strong>📝 Répondu le :</strong><br>
                                {{ \Carbon\Carbon::parse($demande['date_reponse'])->format('d/m/Y à H:i') }}
                            </div>
                            @endif
                            <div>
                                <strong>👤 Demandeur :</strong><br>
                                {{ $demande['demandeur']['name'] }}
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between items-center mt-4 pt-4 border-t">
                            <a href="{{ route('offres.show', $demande['offre']['id']) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                Voir l'offre complète →
                            </a>

                            @if($demande['statut'] === 'acceptee')
                            <div class="text-green-600 font-medium">
                                ✅ Demande acceptée - Vous pouvez contacter {{ $demande['demandeur']['name'] }}
                            </div>
                            @elseif($demande['statut'] === 'refusee')
                            <div class="text-red-600 font-medium">
                                ❌ Demande refusée
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
                <a href="{{ route('demandes.demandes-recues', ['page' => $demandes['current_page'] - 1]) }}"
                   class="px-3 py-2 bg-gray-200 text-gray-700 rounded hover:bg-gray-300">
                    Précédent
                </a>
                @endif

                <span class="px-3 py-2 bg-blue-500 text-white rounded">
                            Page {{ $demandes['current_page'] }} sur {{ $demandes['last_page'] }}
                        </span>

                @if($demandes['current_page'] < $demandes['last_page'])
                <a href="{{ route('demandes.demandes-recues', ['page' => $demandes['current_page'] + 1]) }}"
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2M4 13h2m13-8V4a1 1 0 00-1-1H7a1 1 0 00-1 1v1m10 0H8" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune demande reçue</h3>
                    <p class="text-gray-600 mb-4">Vous n'avez pas encore reçu de demandes sur vos offres. Créez des offres attractives pour recevoir des demandes !</p>
                    <div class="space-x-4">
                        <a href="{{ route('offres.create') }}" class="inline-block bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Créer une offre
                        </a>
                        <a href="{{ route('offres.mes-offres') }}" class="inline-block bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            Voir mes offres
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

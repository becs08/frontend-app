<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $offre['titre'] }}
            </h2>
            @if(isset($can_edit) && $can_edit)
            <div class="flex space-x-2">
                <a href="{{ route('offres.edit', $offre['id']) }}" class="bg-yellow-500 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                    Modifier
                </a>
                <form method="POST" action="{{ route('offres.destroy', $offre['id']) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette offre ?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                        Supprimer
                    </button>
                </form>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Détails de l'offre -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                        <div class="p-6">
                            <!-- En-tête de l'offre -->
                            <div class="flex justify-between items-start mb-4">
                                <div class="flex space-x-2">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                        {{ ucfirst($offre['type_offre']) }}
                                    </span>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ $offre['categorie'] }}
                                    </span>
                                    @if($offre['statut'] === 'active')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                            Active
                                        </span>
                                    @endif
                                </div>
                                @if(isset($offre['prix_formate']))
                                <div class="text-lg font-bold text-green-600">
                                    {{ $offre['prix_formate'] }}
                                </div>
                                @endif
                            </div>

                            <!-- Description -->
                            <div class="mb-6">
                                <h3 class="text-lg font-semibold mb-2">Description</h3>
                                <p class="text-gray-700 whitespace-pre-line">{{ $offre['description'] }}</p>
                            </div>

                            <!-- Informations -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600">
                                <div>
                                    <strong>📍 Localisation :</strong> {{ $offre['localisation'] }}
                                </div>
                                <div>
                                    <strong>👤 Proposé par :</strong> {{ $offre['author_name'] ?? $offre['user']['name'] ?? 'Utilisateur' }}
                                </div>
                                <div>
                                    <strong>📅 Publié le :</strong> {{ \Carbon\Carbon::parse($offre['created_at'])->format('d/m/Y') }}
                                </div>
                                @if($offre['date_expiration'])
                                <div>
                                    <strong>⏰ Expire le :</strong> {{ \Carbon\Carbon::parse($offre['date_expiration'])->format('d/m/Y') }}
                                </div>
                                @endif
                                <div>
                                    <strong>👁️ Vues :</strong> {{ $offre['vues'] ?? 0 }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar : Faire une demande -->
                <div>
                    @if(Session::has('api_token') && (!isset($can_edit) || !$can_edit))
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4">Faire une demande</h3>

                            @if ($errors->any())
                            <div class="mb-4 bg-red-50 border border-red-400 text-red-700 px-4 py-3 rounded text-sm">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                            @endif

                            @if(session('success'))
                            <div class="mb-4 bg-green-50 border border-green-400 text-green-700 px-4 py-3 rounded text-sm">
                                {{ session('success') }}
                            </div>
                            @endif

                            <form method="POST" action="{{ route('demandes.store', $offre['id']) }}" class="space-y-4">
                                @csrf

                                <!-- Message -->
                                <div>
                                    <label for="message" class="block text-sm font-medium text-gray-700">Votre message</label>
                                    <textarea
                                        id="message"
                                        name="message"
                                        rows="4"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        required
                                        placeholder="Expliquez votre besoin...">{{ old('message') }}</textarea>
                                </div>

                                <!-- Prix proposé -->
                                <div>
                                    <label for="prix_propose" class="block text-sm font-medium text-gray-700">Prix proposé (€) - Optionnel</label>
                                    <input
                                        type="number"
                                        id="prix_propose"
                                        name="prix_propose"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                        min="0"
                                        step="0.01"
                                        value="{{ old('prix_propose') }}"
                                        placeholder="Ex: 50.00" />
                                </div>

                                <button type="submit" class="w-full bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Envoyer la demande
                                </button>
                            </form>
                        </div>
                    </div>
                    @elseif(!Session::has('api_token'))
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-center">
                            <h3 class="text-lg font-semibold mb-4">Intéressé par cette offre ?</h3>
                            <p class="text-gray-600 mb-4">Connectez-vous pour contacter le prestataire</p>
                            <a href="{{ route('login') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded block">
                                Se connecter
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Offres similaires -->
            @if(isset($offres_liees) && count($offres_liees) > 0)
            <div class="mt-12">
                <h3 class="text-xl font-semibold mb-6">Offres similaires</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($offres_liees as $offre_liee)
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition-shadow">
                        <div class="p-4">
                            <h4 class="font-semibold text-gray-900 mb-2">
                                <a href="{{ route('offres.show', $offre_liee['id']) }}" class="hover:text-blue-600">
                                    {{ Str::limit($offre_liee['titre'], 50) }}
                                </a>
                            </h4>
                            <p class="text-gray-600 text-sm mb-2">{{ Str::limit($offre_liee['description'], 80) }}</p>
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>{{ $offre_liee['localisation'] }}</span>
                                <span>{{ ucfirst($offre_liee['type_offre']) }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>

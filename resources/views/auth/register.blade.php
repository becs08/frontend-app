<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf

        <!-- En-tête du formulaire -->
        <div class="text-center mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Créer un compte</h2>
            <p class="text-gray-600 mt-2">Rejoignez notre plateforme d'échange de services</p>
        </div>

        <!-- Informations personnelles -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Informations personnelles</h3>

            <!-- Nom -->
            <div>
                <x-input-label for="name" :value="__('Nom complet')" />
                <x-text-input
                    id="name"
                    class="block mt-1 w-full"
                    type="text"
                    name="name"
                    :value="old('name')"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Votre nom complet" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div>
                <x-input-label for="email" :value="__('Adresse email')" />
                <x-text-input
                    id="email"
                    class="block mt-1 w-full"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    autocomplete="username"
                    placeholder="votre@email.com" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Téléphone -->
            <div>
                <x-input-label for="phone" :value="__('Téléphone')" />
                <x-text-input
                    id="phone"
                    class="block mt-1 w-full"
                    type="tel"
                    name="phone"
                    :value="old('phone')"
                    autocomplete="tel"
                    placeholder="+221 XX XXX XX XX" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                <p class="text-sm text-gray-500 mt-1">Optionnel - Facilite les contacts directs</p>
            </div>
        </div>

        <!-- Localisation -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Localisation</h3>

            <!-- Adresse -->
            <div>
                <x-input-label for="address" :value="__('Adresse')" />
                <x-text-input
                    id="address"
                    class="block mt-1 w-full"
                    type="text"
                    name="address"
                    :value="old('address')"
                    placeholder="Votre adresse complète" />
                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                <p class="text-sm text-gray-500 mt-1">Optionnel - Aide pour les services de proximité</p>
            </div>

            <!-- Ville et Code postal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="city" :value="__('Ville')" />
                    <x-text-input
                        id="city"
                        class="block mt-1 w-full"
                        type="text"
                        name="city"
                        :value="old('city')"
                        placeholder="Dakar, Thiès, Saint-Louis..." />
                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                </div>
                <div>
                    <x-input-label for="postal_code" :value="__('Code postal')" />
                    <x-text-input
                        id="postal_code"
                        class="block mt-1 w-full"
                        type="text"
                        name="postal_code"
                        :value="old('postal_code')"
                        placeholder="12345" />
                    <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                </div>
            </div>
        </div>

        <!-- Type de compte -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Type de compte</h3>

            <div>
                <x-input-label for="user_type" :value="__('Je souhaite')" />
                <select
                    id="user_type"
                    name="user_type"
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    required>
                    <option value="">Choisissez votre type de compte</option>
                    <option value="demandeur" {{ old('user_type') === 'demandeur' ? 'selected' : '' }}>
                    🔍 Demandeur - Rechercher des services
                    </option>
                    <option value="offreur" {{ old('user_type') === 'offreur' ? 'selected' : '' }}>
                    💼 Offreur - Proposer des services
                    </option>
                </select>
                <x-input-error :messages="$errors->get('user_type')" class="mt-2" />
                <div class="mt-2 text-sm text-gray-600">
                    <p><strong>Demandeur :</strong> Vous cherchez des services ou produits</p>
                    <p><strong>Offreur :</strong> Vous proposez vos services ou produits</p>
                </div>
            </div>
        </div>

        <!-- Sécurité -->
        <div class="space-y-4">
            <h3 class="text-lg font-medium text-gray-900 border-b pb-2">Sécurité</h3>

            <!-- Mot de passe -->
            <div>
                <x-input-label for="password" :value="__('Mot de passe')" />
                <x-text-input
                    id="password"
                    class="block mt-1 w-full"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Au moins 8 caractères" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                <p class="text-sm text-gray-500 mt-1">Minimum 8 caractères</p>
            </div>

            <!-- Confirmation du mot de passe -->
            <div>
                <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />
                <x-text-input
                    id="password_confirmation"
                    class="block mt-1 w-full"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Retapez votre mot de passe" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-between pt-6 border-t">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mb-4 sm:mb-0"
               href="{{ route('login') }}">
                {{ __('Déjà inscrit ? Se connecter') }}
            </a>

            <x-primary-button class="w-full sm:w-auto px-8 py-3">
                {{ __('Créer mon compte') }}
            </x-primary-button>
        </div>

        <!-- Note de confidentialité -->
        <div class="text-center text-xs text-gray-500 pt-4 border-t">
            <p>En créant un compte, vous acceptez nos conditions d'utilisation et notre politique de confidentialité.</p>
        </div>
    </form>

    <!-- Script pour améliorer l'UX -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validation en temps réel du mot de passe
            const password = document.getElementById('password');
            const passwordConfirm = document.getElementById('password_confirmation');

            function validatePasswords() {
                if (password.value && passwordConfirm.value) {
                    if (password.value === passwordConfirm.value) {
                        passwordConfirm.setCustomValidity('');
                    } else {
                        passwordConfirm.setCustomValidity('Les mots de passe ne correspondent pas');
                    }
                }
            }

            password.addEventListener('input', validatePasswords);
            passwordConfirm.addEventListener('input', validatePasswords);

            // Format automatique du téléphone
            const phoneInput = document.getElementById('phone');
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.startsWith('221')) {
                    value = '+' + value;
                } else if (value.length > 0 && !value.startsWith('221')) {
                    value = '+221' + value;
                }
                e.target.value = value;
            });
        });
    </script>
</x-guest-layout>

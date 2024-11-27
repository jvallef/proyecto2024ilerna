<form method="POST" action="{{ isset($user) ? route('admin.users.update', $user) : route('admin.users.store') }}" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @if(isset($user))
        @method('PUT')
    @endif

    <!-- Información Básica -->
    <div class="border-b border-gray-200 pb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información básica</h3>
        
        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
            <!-- Nombre -->
            <div>
                <div class="flex items-center">
                    <x-input-label for="name" :value="__('Nombre')" />
                    <x-required-mark />
                </div>
                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name ?? '')" required autofocus autocomplete="name" />
                <x-input-error :messages="$errors->get('name')" class="mt-2" />
            </div>

            <!-- Email -->
            <div>
                <div class="flex items-center">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-required-mark />
                </div>
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email ?? '')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <!-- Teléfono -->
            <div>
                <x-input-label for="phone" :value="__('Teléfono')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone', $user->phone ?? '')" autocomplete="tel" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            @if(!isset($user))
            <div class="sm:col-span-2 grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Contraseña -->
                <div>
                    <div class="flex items-center">
                        <x-input-label for="password" :value="__('Contraseña')" />
                        <x-required-mark />
                    </div>
                    <x-text-input id="password" class="block mt-1 w-full"
                        type="password"
                        name="password"
                        required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <div class="flex items-center">
                        <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                        <x-required-mark />
                    </div>
                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                        type="password"
                        name="password_confirmation"
                        required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>
            @endif

            <!-- Roles -->
            <div>
                <div class="flex items-center">
                    <x-input-label for="roles" :value="__('Roles')" />
                    <x-required-mark />
                </div>
                <select id="roles" name="roles[]" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" multiple required>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ isset($user) && $user->hasRole($role->name) ? 'selected' : '' }}>
                            {{ ucfirst($role->name) }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('roles')" class="mt-2" />
            </div>
        </div>
    </div>

    <!-- Avatar -->
    <div class="border-b border-gray-200 pb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Avatar</h3>
        <x-media.avatar-upload :value="$user->avatar ?? null" />
    </div>

    <!-- Perfil -->
    <div class="border-b border-gray-200 pb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Perfil</h3>
        
        <!-- Biografía -->
        <div class="mt-4">
            <x-input-label for="profile_bio" :value="__('Biografía')" />
            <textarea
                id="profile_bio"
                name="profile[bio]"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >{{ old('profile.bio', $user->profile['bio'] ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('profile.bio')" class="mt-2" />
        </div>

        <!-- Redes Sociales -->
        <div class="mt-6">
            <h5 class="text-sm font-medium text-gray-700 mb-4">Redes Sociales</h5>
            
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- LinkedIn -->
                <div>
                    <x-input-label for="profile_social_linkedin" :value="__('LinkedIn')" />
                    <x-text-input 
                        id="profile_social_linkedin" 
                        class="block mt-1 w-full" 
                        type="url" 
                        name="profile[social][linkedin]" 
                        :value="old('profile.social.linkedin', $user->profile['social']['linkedin'] ?? '')" 
                        placeholder="https://linkedin.com/in/username" 
                    />
                    <x-input-error :messages="$errors->get('profile.social.linkedin')" class="mt-2" />
                </div>

                <!-- Twitter -->
                <div>
                    <x-input-label for="profile_social_twitter" :value="__('Twitter')" />
                    <x-text-input 
                        id="profile_social_twitter" 
                        class="block mt-1 w-full" 
                        type="url" 
                        name="profile[social][twitter]" 
                        :value="old('profile.social.twitter', $user->profile['social']['twitter'] ?? '')" 
                        placeholder="https://twitter.com/username" 
                    />
                    <x-input-error :messages="$errors->get('profile.social.twitter')" class="mt-2" />
                </div>

                <!-- GitHub -->
                <div>
                    <x-input-label for="profile_social_github" :value="__('GitHub')" />
                    <x-text-input 
                        id="profile_social_github" 
                        class="block mt-1 w-full" 
                        type="url" 
                        name="profile[social][github]" 
                        :value="old('profile.social.github', $user->profile['social']['github'] ?? '')" 
                        placeholder="https://github.com/username" 
                    />
                    <x-input-error :messages="$errors->get('profile.social.github')" class="mt-2" />
                </div>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end space-x-4">
        <x-secondary-button onclick="window.history.back()" type="button">
            {{ __('Cancelar') }}
        </x-secondary-button>
        <x-primary-button>
            {{ isset($user) ? __('Actualizar') : __('Crear') }}
        </x-primary-button>
    </div>
</form>

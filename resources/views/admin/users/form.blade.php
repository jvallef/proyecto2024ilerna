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
            <div class="sm:col-span-1">
                <x-input-label for="phone" :value="__('Teléfono')" />
                <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone', $user->phone ?? '')" autocomplete="tel" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>

            @if(!isset($user))
            <div class="sm:col-span-2 grid grid-cols-1 gap-6 sm:grid-cols-2">
            @else
            <div class="sm:col-span-2 border-t border-gray-200 mt-6 pt-6">
                <h4 class="text-sm font-medium text-gray-900 mb-4">Cambiar Contraseña (opcional)</h4>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
            @endif
                <!-- Contraseña -->
                <div>
                    <div class="flex items-center">
                        <x-input-label for="password" :value="__('Contraseña')" />
                        @if(!isset($user))
                            <x-required-mark />
                        @endif
                    </div>
                    <x-text-input id="password" class="block mt-1 w-full"
                        type="password"
                        name="password"
                        :required="!isset($user)"
                        autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    @if(isset($user))
                        <p class="mt-1 text-sm text-gray-500">Dejar en blanco para mantener la contraseña actual</p>
                    @endif
                </div>

                <!-- Confirmar Contraseña -->
                <div>
                    <div class="flex items-center">
                        <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                        @if(!isset($user))
                            <x-required-mark />
                        @endif
                    </div>
                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                        type="password"
                        name="password_confirmation"
                        :required="!isset($user)"
                        autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>
            </div>
            @if(isset($user))
                </div>
            @endif
        </div>
    </div>

    <!-- Avatar -->
    <div class="border-b border-gray-200 pb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Avatar</h3>
        <x-media.avatar-upload :model="$user ?? null" />
    </div>

    <!-- Roles -->
    <div class="border-b border-gray-200 pb-8">
        <div class="flex items-center">
            <h3 class="text-lg font-semibold text-gray-900">Roles</h3>
            <x-required-mark />
        </div>
        <div class="mt-4 grid grid-cols-2 gap-4">
            @foreach($roles as $role)
                <div class="flex items-center">
                    <input 
                        id="role_{{ $role->name }}" 
                        name="roles[]" 
                        type="checkbox" 
                        value="{{ $role->name }}"
                        class="h-4 w-4 rounded border-gray-300 text-secondary focus:ring-secondary"
                        {{ (isset($user) && $user->hasRole($role->name)) || (old('roles') && in_array($role->name, old('roles', []))) ? 'checked' : '' }}
                    >
                    <label for="role_{{ $role->name }}" class="ml-3 text-sm font-medium text-gray-700">
                        {{ ucfirst($role->name) }}
                    </label>
                </div>
            @endforeach
            <x-input-error :messages="$errors->get('roles')" class="mt-2" />
        </div>
    </div>

    <!-- Perfil -->
    <div class="border-b border-gray-200 pb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Perfil</h3>
        
        <!-- Biografía -->
        <div class="mt-4 max-w-xl">
            <x-input-label for="profile_bio" :value="__('Biografía')" />
            <textarea
                id="profile_bio"
                name="profile[bio]"
                rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-secondary focus:ring-secondary"
            >{{ old('profile.bio', $user->profile['bio'] ?? '') }}</textarea>
            <x-input-error :messages="$errors->get('profile.bio')" class="mt-2" />
        </div>
    </div>

    <!-- Redes Sociales -->
    <div class="border-b border-gray-200 pb-8">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Redes Sociales</h3>
        
        <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
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

    <!-- Botones -->
    <div class="flex items-center justify-end gap-x-6">
        <x-secondary-button onclick="window.history.back()" type="button" class="bg-gray-100 hover:bg-gray-200 text-gray-700">
            {{ __('Cancelar') }}
        </x-secondary-button>
        <x-primary-button class="bg-secondary hover:bg-secondary/90">
            {{ isset($user) ? __('Actualizar') : __('Crear') }}
        </x-primary-button>
    </div>
</form>
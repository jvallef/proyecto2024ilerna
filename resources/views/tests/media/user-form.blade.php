@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h3 class="text-lg font-medium">Test - Formulario de Usuario</h3>
                
                <form method="POST" action="{{ route('test.user.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <!-- Información Básica -->
                    <div class="border-b border-gray-200 pb-6">
                        <h4 class="text-sm font-medium text-gray-900">Información Básica</h4>
                        
                        <div class="mt-4 grid grid-cols-1 gap-y-6 sm:grid-cols-2 sm:gap-x-4">
                            <div>
                                <x-input-label for="name" :value="__('Nombre')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="phone" :value="__('Teléfono')" />
                                <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" autocomplete="tel" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Contraseña')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirmar Contraseña')" />
                                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Avatar -->
                    <div class="border-b border-gray-200 pb-6">
                        <h4 class="text-sm font-medium text-gray-900 mb-4">Avatar</h4>
                        <x-media.avatar-upload />
                    </div>

                    <!-- Perfil -->
                    <div class="border-b border-gray-200 pb-6">
                        <h4 class="text-sm font-medium text-gray-900">Perfil</h4>
                        
                        <div class="mt-4">
                            <x-input-label for="profile_bio" :value="__('Biografía')" />
                            <textarea
                                id="profile_bio"
                                name="profile[bio]"
                                rows="3"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            >{{ old('profile.bio') }}</textarea>
                            <x-input-error :messages="$errors->get('profile.bio')" class="mt-2" />
                        </div>

                        <div class="mt-4">
                            <h5 class="text-sm font-medium text-gray-700">Redes Sociales</h5>
                            
                            <div class="mt-2 space-y-4">
                                <div>
                                    <x-input-label for="profile_social_linkedin" :value="__('LinkedIn')" />
                                    <x-text-input 
                                        id="profile_social_linkedin" 
                                        class="block mt-1 w-full" 
                                        type="url" 
                                        name="profile[social][linkedin]" 
                                        :value="old('profile.social.linkedin')" 
                                        placeholder="https://linkedin.com/in/username" 
                                    />
                                    <x-input-error :messages="$errors->get('profile.social.linkedin')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="profile_social_twitter" :value="__('Twitter')" />
                                    <x-text-input 
                                        id="profile_social_twitter" 
                                        class="block mt-1 w-full" 
                                        type="url" 
                                        name="profile[social][twitter]" 
                                        :value="old('profile.social.twitter')" 
                                        placeholder="https://twitter.com/username" 
                                    />
                                    <x-input-error :messages="$errors->get('profile.social.twitter')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="profile_social_github" :value="__('GitHub')" />
                                    <x-text-input 
                                        id="profile_social_github" 
                                        class="block mt-1 w-full" 
                                        type="url" 
                                        name="profile[social][github]" 
                                        :value="old('profile.social.github')" 
                                        placeholder="https://github.com/username" 
                                    />
                                    <x-input-error :messages="$errors->get('profile.social.github')" class="mt-2" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-secondary-button onclick="window.history.back()" type="button" class="mr-3">
                            {{ __('Cancelar') }}
                        </x-secondary-button>
                        <x-primary-button>
                            {{ __('Crear') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

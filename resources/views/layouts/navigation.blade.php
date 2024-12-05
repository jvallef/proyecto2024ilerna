<nav x-data="{ open: false }" class="bg-primary text-white">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ url('/') }}">
                        <x-application-logo class="block h-9 w-auto" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px pb-2 sm:ml-10 sm:flex">
                    @php
                        $activeRole = Auth::check() ? session('active_role', Auth::user()->roles->first()->name ?? null) : null;
                    @endphp

                    @auth
                        @if($activeRole === 'admin')
                            <x-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-white hover:text-gray-200">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="text-white hover:text-gray-200">
                                {{ __('Usuarios') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.areas.index')" :active="request()->routeIs('admin.areas.*')" class="text-white hover:text-gray-200">
                                {{ __('Areas') }}
                            </x-nav-link>
                            <x-nav-link :href="route('admin.paths.index')" :active="request()->routeIs('admin.paths.*')" class="text-white hover:text-gray-200">
                                {{ __('Rutas') }}
                            </x-nav-link>
                        @elseif($activeRole === 'teacher')
                            <x-nav-link :href="route('workarea.dashboard')" :active="request()->routeIs('workarea.dashboard')" class="text-white hover:text-gray-200">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        @elseif($activeRole === 'student')
                            <x-nav-link :href="route('classroom.dashboard')" :active="request()->routeIs('classroom.dashboard')" class="text-white hover:text-gray-200">
                                {{ __('Dashboard') }}
                            </x-nav-link>
                        @endif
                    @endauth
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                @auth
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 text-sm leading-4 font-medium rounded-md text-white hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ Auth::user()->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @if(Auth::user()->roles->count() > 1)
                                <div class="px-4 py-2 text-xs text-gray-500">
                                    {{ __('Cambiar Rol') }}
                                </div>

                                @foreach(Auth::user()->roles as $role)
                                    <x-dropdown-link 
                                        href="{{ route('switch.role', $role->name) }}"
                                        class="{{ session('active_role') === $role->name ? 'bg-gray-100' : '' }}"
                                    >
                                        <div class="flex items-center justify-between">
                                            {{ __(ucfirst($role->name)) }}
                                            @if(session('active_role') === $role->name)
                                                <svg class="ml-2 h-4 w-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            @endif
                                        </div>
                                    </x-dropdown-link>
                                @endforeach

                                <div class="border-t border-gray-200 my-1"></div>
                            @endif

                            <x-dropdown-link :href="route('profile.edit')">
                                {{ __('Profile') }}
                            </x-dropdown-link>

                            <!-- Authentication -->
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <x-dropdown-link :href="route('logout')"
                                        onclick="event.preventDefault();
                                                    this.closest('form').submit();">
                                    {{ __('Log Out') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                @else
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 text-sm leading-4 font-medium rounded-md text-white hover:text-gray-200 focus:outline-none transition ease-in-out duration-150">
                                <div>{{ __('Login') }}</div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('login')">
                                {{ __('Iniciar Sesión') }}
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('register')">
                                {{ __('Registrarse') }}
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                @endauth
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-opacity-10 focus:outline-none focus:bg-opacity-20 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-1 space-y-1">
            @php
                $activeRole = Auth::check() ? session('active_role', Auth::user()->roles->first()->name ?? null) : null;
            @endphp

            @auth
                @if($activeRole === 'admin')
                    <x-responsive-nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" class="text-white hover:text-gray-200">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" class="text-white hover:text-gray-200">
                        {{ __('Usuarios') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.areas.index')" :active="request()->routeIs('admin.areas.*')" class="text-white hover:text-gray-200">
                        {{ __('Areas') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('admin.paths.index')" :active="request()->routeIs('admin.paths.*')" class="text-white hover:text-gray-200">
                        {{ __('Rutas') }}
                    </x-responsive-nav-link>
                @elseif($activeRole === 'teacher')
                    <x-responsive-nav-link :href="route('workarea.dashboard')" :active="request()->routeIs('workarea.dashboard')" class="text-white hover:text-gray-200">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                @elseif($activeRole === 'student')
                    <x-responsive-nav-link :href="route('classroom.dashboard')" :active="request()->routeIs('classroom.dashboard')" class="text-white hover:text-gray-200">
                        {{ __('Dashboard') }}
                    </x-responsive-nav-link>
                @endif
            @endauth
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-white/10">
            @auth
                <div class="px-4">
                    <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                    <div class="font-medium text-sm text-gray-200">{{ Auth::user()->email }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    @if(Auth::user()->roles->count() > 1)
                        <div class="px-4 py-2 text-xs text-gray-400">
                            {{ __('Cambiar Rol') }}
                        </div>

                        @foreach(Auth::user()->roles as $role)
                            <x-responsive-nav-link 
                                href="{{ route('switch.role', $role->name) }}"
                                class="{{ session('active_role') === $role->name ? 'bg-gray-100/10' : '' }}"
                            >
                                <div class="flex items-center justify-between">
                                    {{ __(ucfirst($role->name)) }}
                                    @if(session('active_role') === $role->name)
                                        <svg class="ml-2 h-4 w-4 text-green-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @endif
                                </div>
                            </x-responsive-nav-link>
                        @endforeach

                        <div class="border-t border-white/10 my-1"></div>
                    @endif

                    <x-responsive-nav-link :href="route('profile.edit')" class="text-white hover:text-gray-200">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <!-- Authentication -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf

                        <x-responsive-nav-link :href="route('logout')"
                                onclick="event.preventDefault();
                                            this.closest('form').submit();" class="text-white hover:text-gray-200">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            @else
                <div class="px-4">
                    <div class="font-medium text-base text-white">{{ __('Login') }}</div>
                </div>

                <div class="mt-3 space-y-1">
                    <x-responsive-nav-link :href="route('login')" class="text-white hover:text-gray-200">
                        {{ __('Iniciar Sesión') }}
                    </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('register')" class="text-white hover:text-gray-200">
                        {{ __('Registrarse') }}
                    </x-responsive-nav-link>
                </div>
            @endauth
        </div>
    </div>
</nav>

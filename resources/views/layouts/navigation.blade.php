<nav x-data="{ open: false }" class="bg-white border-b-2 border-black">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Navigation Links -->
                <div class="hidden gap-8 sm:-my-px sm:flex">
                    <x-nav-link :href="route('events.search')" :active="request()->routeIs('events.search')">
                        {{ __('Découvrir') }}
                    </x-nav-link>
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                    <x-nav-link :href="route('organizations.index')" :active="request()->routeIs('organizations.*')">
                        {{ __('Organisations') }}
                    </x-nav-link>
                    <x-nav-link :href="route('events.index')" :active="request()->routeIs('events.*') && ! request()->routeIs('events.search')">
                        {{ __('Events') }}
                    </x-nav-link>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:gap-3 sm:ms-6">
                @if ($currentOrganization)
                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button class="inline-flex items-center px-3 py-2 border-2 border-black text-xs leading-4 font-bold uppercase tracking-wider rounded-lg text-black bg-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2 transition-all duration-150">
                                <div>{{ $currentOrganization->name }}</div>

                                <div class="ms-1">
                                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            @foreach ($navOrganizations as $organization)
                                <form method="POST" action="{{ route('organizations.switch', $organization) }}">
                                    @csrf
                                    @method('put')
                                    <button type="submit" class="block w-full px-4 py-2 text-start text-sm leading-5 hover:bg-neutral-100 focus:outline-none focus:bg-neutral-100 {{ $organization->is($currentOrganization) ? 'font-semibold text-black' : 'text-neutral-700' }}">
                                        {{ $organization->name }}
                                    </button>
                                </form>
                            @endforeach
                        </x-slot>
                    </x-dropdown>
                @endif

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border-2 border-black text-xs leading-4 font-bold uppercase tracking-wider rounded-lg text-black bg-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] hover:-translate-y-0.5 hover:shadow-[3px_3px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus-visible:ring-2 focus-visible:ring-black focus-visible:ring-offset-2 transition-all duration-150">
                           @auth
    <span>{{ Auth::user()->name }}</span>
    {{-- liens de déconnexion, profil, etc. --}}
@else
    <a href="{{ route('login') }}" class="text-neutral-600 hover:text-black">Connexion</a>
    <a href="{{ route('register') }}" class="ml-4 text-neutral-600 hover:text-black">Inscription</a>
@endauth

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
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
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 border-2 border-black rounded-lg text-black bg-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] focus:outline-none focus-visible:ring-2 focus-visible:ring-black transition-all duration-150">
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
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('events.search')" :active="request()->routeIs('events.search')">
                {{ __('Découvrir') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('organizations.index')" :active="request()->routeIs('organizations.*')">
                {{ __('Organisations') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t-2 border-black">
            <div class="px-4">
                @auth
    <span>{{ Auth::user()->name }}</span>
    {{-- liens de déconnexion, profil, etc. --}}
@else
    <a href="{{ route('login') }}" class="text-neutral-600 hover:text-black">Connexion</a>
    <a href="{{ route('register') }}" class="ml-4 text-neutral-600 hover:text-black">Inscription</a>
@endauth
@auth
                <div class="font-medium text-sm text-neutral-500">{{ Auth::user()->email }}</div>
                @else
                <div class="font-medium text-sm text-neutral-500">Utilisateur non connecté</div>
                @endauth
            </div>

            @if ($currentOrganization)
                <div class="mt-3 space-y-1">
                    <div class="px-4 text-xs font-semibold uppercase tracking-wider text-neutral-500">{{ __('Organisation active') }}</div>
                    @foreach ($navOrganizations as $organization)
                        <form method="POST" action="{{ route('organizations.switch', $organization) }}">
                            @csrf
                            @method('put')
                            <button type="submit" class="block w-full ps-3 pe-4 py-2 border-l-4 text-start text-base font-medium {{ $organization->is($currentOrganization) ? 'border-black font-semibold text-black bg-neutral-100' : 'border-transparent text-neutral-600 hover:bg-neutral-100' }}">
                                {{ $organization->name }}
                            </button>
                        </form>
                    @endforeach
                </div>
            @endif

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

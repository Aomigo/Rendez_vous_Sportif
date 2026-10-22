<x-guest-layout>
    <h1 class="text-lg font-semibold text-black">{{ config('app.name') }}</h1>

    <div class="mt-4 flex gap-4">
        @auth
            <a href="{{ route('dashboard') }}" class="underline text-neutral-700">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="underline text-neutral-700">Log in</a>
            @if (Route::has('register'))
                <a href="{{ route('register') }}" class="underline text-neutral-700">Register</a>
            @endif
        @endauth
    </div>
</x-guest-layout>

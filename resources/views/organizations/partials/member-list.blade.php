<section class="max-w-xl">
    <header>
        <h2 class="text-lg font-bold text-black tracking-tight">{{ __('Membres') }}</h2>
    </header>

    <ul class="mt-4 divide-y divide-black/15">
        @foreach ($members as $member)
            <li class="flex items-center justify-between gap-4 py-3">
                <div>
                    <p class="text-black">{{ $member->name }}</p>
                    <p class="text-sm text-neutral-600">{{ $member->email }} · {{ $member->isOwner ? __('Owner') : __('Membre') }}</p>
                </div>

                @unless ($member->isOwner)
                    <form method="POST" action="{{ route('organizations.members.destroy', [$organization->id, $member->id]) }}">
                        @csrf
                        @method('delete')
                        <x-danger-button>{{ __('Retirer') }}</x-danger-button>
                    </form>
                @endunless
            </li>
        @endforeach
    </ul>
    <x-input-error class="mt-2" :messages="$errors->get('member')" />

    <form method="POST" action="{{ route('organizations.members.store', $organization->id) }}" class="mt-6 space-y-4">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Ajouter un membre par e-mail')" />
            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <x-primary-button>{{ __('Ajouter') }}</x-primary-button>
    </form>
</section>

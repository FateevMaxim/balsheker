<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            {{ __('Информация профиля') }}
        </h2>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('post')

        <div>
            <x-input-label for="name" :value="__('Имя')" />
            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>
        <div>
            <x-input-label for="surname" :value="__('Фамилия')" />
            <x-text-input id="surname" name="surname" type="text" class="mt-1 block w-full" :value="old('surname', $user->surname)" required  autocomplete="surname" />
            <x-input-error class="mt-2" :messages="$errors->get('surname')" />
        </div>

        <div>
            <x-input-label for="login" :value="__('Номер телефона')" />
            <x-text-input id="login" name="login" type="text" class="mt-1 block w-full" :value="old('login', $user->login)" required autocomplete="login" />
            <x-input-error class="mt-2" :messages="$errors->get('login')" />
        </div>

        <div>
            <x-input-label for="city" :value="__('Город')" />
            <select id="city" name="city" class="block mt-1 w-full border-2 border-gray-300 rounded-md" required>
                @foreach($cities as $city)
                    <option value="{{ $city->name }}" {{ old('city', $user->city) == $city->name ? 'selected' : '' }}>
                        {{ $city->name }}
                    </option>
                @endforeach
            </select>
            <x-input-error class="mt-2" :messages="$errors->get('city')" />
        </div>
        <x-primary-button>{{ __('Сохранить') }}</x-primary-button>
    </form>
</section>

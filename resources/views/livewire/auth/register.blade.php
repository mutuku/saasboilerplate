<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use App\Models\Tenant;
use App\Services\CountryService;
use App\Services\CurrencyService;
use Illuminate\Database\Eloquent\Collection;

new #[Layout('components.layouts.auth')] class extends Component {
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $tenant_name = '';
    public string $country_id = '';
    public string $currency_id = '';
    public string $measurement_system = '';

    public Collection $countries;
    public Collection $currencies;

    public function mount(): void
    {
        $countryService = app(CountryService::class);
        $currencyService = app(CurrencyService::class);

        // Get all countries and currencies using the direct methods
        $this->countries = $countryService->getAllCountries();
        $this->currencies = $currencyService->getAllCurrencies();
    }

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
            'tenant_name' => ['required', 'string', 'max:255'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'measurement_system' => ['required', 'string', 'max:50'],
        ]);

        $tenant = Tenant::create([
            'name' => $this->tenant_name,
            'country_id' => $this->country_id,
            'currency_id' => $this->currency_id,
            'measurement_system' => $this->measurement_system,
            // Add other tenant attributes as needed
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['tenant_id'] = $tenant->id;
        $validated['is_tenant_admin'] = 1;

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        $this->redirectIntended(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div class="flex flex-col gap-6">
    <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

    <!-- Session Status -->
    <x-auth-session-status class="text-center" :status="session('status')" />

    <form wire:submit="register" class="flex flex-col gap-6">
        <!-- Name -->
        <flux:input
            wire:model="name"
            :label="__('Name')"
            type="text"
            required
            autofocus
            autocomplete="name"
            :placeholder="__('Full name')"
        />

        <!-- Email Address -->
        <flux:input
            wire:model="email"
            :label="__('Email address')"
            type="email"
            required
            autocomplete="email"
            placeholder="email@example.com"
        />

        <!-- Tenant Name -->
        <flux:input
            wire:model="tenant_name"
            :label="__('Organization / Company Name')"
            type="text"
            required
            autocomplete="organization"
            :placeholder="__('Company or Group Name')"
        />

        <!-- Country -->
        <flux:select
            wire:model="country_id"
            :label="__('Country')"
            required
        >
            <option value="">{{ __('Select country') }}</option>
            @foreach($this->countries as $country)
                <option value="{{ $country->id }}">{{ $country->name }} ({{ $country->code }})</option>
            @endforeach
        </flux:select>

        <!-- Currency -->
        <flux:select
            wire:model="currency_id"
            :label="__('Default Currency')"
            required
        >
            <option value="">{{ __('Select currency') }}</option>
            @foreach($this->currencies as $currency)
                <option value="{{ $currency->id }}">{{ $currency->iso_code }} - {{ $currency->name }} ({{ $currency->symbol }})</option>
            @endforeach
        </flux:select>

        <!-- Measurement System -->
        <flux:select
            wire:model="measurement_system"
            :label="__('Measurement System')"
            required
        >
            <option value="">{{ __('Select measurement system') }}</option>
            <option value="metric">{{ __('Metric (centimeters, kilograms, etc.)') }}</option>
            <option value="imperial">{{ __('Imperial (inches, pounds, etc.)') }}</option>
        </flux:select>

        <!-- Password -->
        <flux:input
            wire:model="password"
            :label="__('Password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Password')"
        />

        <!-- Confirm Password -->
        <flux:input
            wire:model="password_confirmation"
            :label="__('Confirm password')"
            type="password"
            required
            autocomplete="new-password"
            :placeholder="__('Confirm password')"
        />

        <div class="flex items-center justify-end">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create account') }}
            </flux:button>
        </div>
    </form>

    <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
        {{ __('Already have an account?') }}
        <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
    </div>
</div>

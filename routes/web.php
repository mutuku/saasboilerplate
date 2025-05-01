<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use Lab404\Impersonate\Services\ImpersonateManager;

/*
Route::get('/', function () {
    return view('welcome');
})->name('home');
*/

Route::get('/', function () {
    return redirect()->route('login');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified', 'tenant.scope'])
    ->name('dashboard');



Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');
});

Route::prefix('admin')->middleware(['auth', 'verified', 'ensure.superadmin'])->group(function () {
    Route::view('/', 'admin.dashboard')->name('admin.index');
    Route::view('plans', 'admin.plans')->name('admin.plans');
    Route::view('users', 'admin.users')->name('admin.users');
    Route::view('countries', 'admin.countries')->name('admin.countries');
    Route::view('currencies', 'admin.currencies')->name('admin.currencies');

    // Other admin routes can go here
    //Route::view('settings', 'admin.settings')->name('admin.settings');
    // Add any other admin routes here
});

//Route::impersonate();

Route::middleware(['auth', 'ensure.superadmin'])->group(function () {
    Route::get('/impersonate/{id}', function ($id) {
        $user = \App\Models\User::findOrFail($id);

        if (!auth()->user()->canImpersonate() || !$user->canBeImpersonated()) {
            abort(403);
        }

        auth()->user()->impersonate($user);
        return redirect('/dashboard'); // or wherever you want
    })->name('impersonate');

    Route::get('/leave-impersonation', function () {
        auth()->user()->leaveImpersonation();
        return redirect('/admin'); // or wherever you want
    })->name('impersonate.leave');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/leave-impersonation', function () {
        auth()->user()->leaveImpersonation();
        return redirect('/admin'); // or wherever you want
    })->name('impersonate.leave');
});




require __DIR__.'/auth.php';

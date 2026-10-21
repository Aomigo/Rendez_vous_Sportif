<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [EventController::class, 'search'])->name('events.search');



Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('organizations', OrganizationController::class)->except('show');
    Route::put('/organizations/{organization}/switch', [OrganizationController::class, 'switch'])->name('organizations.switch');
    Route::post('/organizations/{organization}/members', [OrganizationController::class, 'addMember'])->name('organizations.members.store');
    Route::delete('/organizations/{organization}/members/{user}', [OrganizationController::class, 'removeMember'])->name('organizations.members.destroy');

    Route::resource('events', EventController::class)->except('show');
    Route::match(['put', 'patch', 'post', 'delete'], '/events/{event}/cancel', [EventController::class, 'cancel'])->name('events.cancel');
});

require __DIR__.'/auth.php';

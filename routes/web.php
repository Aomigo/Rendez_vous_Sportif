<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [EventController::class, 'search'])->name('events.search');

Route::get('/e/{token}', [EventController::class, 'details'])->name('events.details');


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
    Route::post('/e/{token}/join', [EventController::class, 'join'])->name('events.join')->middleware('auth');
    Route::delete('/e/{token}/leave', [EventController::class, 'leave'])->name('events.leave')->middleware('auth');
    Route::match(['put', 'patch', 'post', 'delete'], '/events/{event}/cancel', [EventController::class, 'cancel'])->name('events.cancel');
});

require __DIR__.'/auth.php';

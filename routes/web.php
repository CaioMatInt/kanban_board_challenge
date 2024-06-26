<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    //@@TODO: Should check how to change the home path
    Route::get('dashboard', \App\Livewire\Boards::class)->middleware('auth')->name('dashboard');
});
Route::get('boards', \App\Livewire\Boards::class)->middleware('auth');
Route::get('boards/{id}', \App\Livewire\ShowBoards::class)->middleware('auth')->name('boards.show');

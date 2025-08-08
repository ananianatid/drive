<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentController;

Route::get('/', function () {
    return view('welcome');
});

// Public Student pages
Route::prefix('etudiants')->name('students.')->group(function () {
    Route::get('/', [StudentController::class, 'index'])->name('index');
    Route::get('/{student}', [StudentController::class, 'show'])->name('show');
});

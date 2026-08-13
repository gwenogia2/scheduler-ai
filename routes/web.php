<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ScheduleController;

// Dashboard
Route::get('/', [ScheduleController::class, 'index'])->name('schedules.index');

// Endpoint untuk simpan tugas baru
Route::post('/tasks', [ScheduleController::class, 'storeTask'])->name('tasks.store');

// Endpoint untuk AI membuat rekomendasi jadwal
Route::post('/schedules/generate', [ScheduleController::class, 'generate'])->name('schedules.generate');

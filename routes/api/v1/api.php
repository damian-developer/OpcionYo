<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\AppointmentController;

Route::prefix('/v1')->group(function () {
    Route::get('appointments/avalaible', [AppointmentController::class, 'avalaibleAppointment']);
    Route::get('doctors/avalaible', [DoctorController::class, 'avalaibleDoctors']);
    Route::get('doctors/export', [DoctorController::class, 'exportDoctorsSchedule']);
});

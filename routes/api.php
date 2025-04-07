<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\CommonController;

Route::post('/register', [CommonController::class, 'register']);
Route::post('/login', [CommonController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/sessionsForDoctor/{doctor}', [CommonController::class, 'sessionsForDoctor']);
    Route::get('/sessionsForClinic/{clinic}', [CommonController::class, 'sessionsForClinic']);
    Route::get('/doctorsForClinic/{clinic}', [CommonController::class, 'doctorsForClinic']);
    Route::get('/doctorsForClinicNurse', [CommonController::class, 'doctorsForClinicNurse']);
    Route::get('/allDoctors', [CommonController::class, 'allDoctors']);
    Route::get('/allClinics', [CommonController::class, 'allClinics']);
    Route::get('/mySession', [CommonController::class, 'getNearstSessionForDoc']);
    Route::get('/appointments/{session}', [CommonController::class, 'appointments']);
    Route::put('/setSessionAvailability/{session}', [CommonController::class, 'setSessionAvailability']);
    Route::get('/categoriesOfClinic/{clinic}', [CommonController::class, 'categoriesOfClinic']);
    Route::post('/appointment', [CommonController::class, 'newAppointment']);
    Route::put('/startAppointment/{appointment}', [CommonController::class, 'startAppointment']);
    Route::put('/endAppointment/{appointment}', [CommonController::class, 'endAppointment']);
    Route::get('/categories', [CommonController::class, 'categories']);


    Route::post('/logout', [CommonController::class, 'logout']);
});

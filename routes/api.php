<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\MedicalDocumentController;
use App\Http\Controllers\Api\PrescriptionController;
use App\Http\Controllers\Api\PrescriptionPdfController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\AccessPermissionController;
use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\DoctorScheduleController;
use App\Http\Controllers\Api\DoctorAvailabilityController;
use App\Http\Controllers\Api\DoctorUnavailabilityController;

Route::post('/register/patient', [AuthController::class, 'registerPatient']);
Route::post('/register/doctor', [AuthController::class, 'registerDoctor']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/consultations', [ConsultationController::class, 'index']);
    Route::post('/consultations', [ConsultationController::class, 'store']);
    Route::get('/consultations/{consultation}', [ConsultationController::class, 'show']);
    Route::get('/medical-documents', [MedicalDocumentController::class, 'index']);
    Route::post('/medical-documents', [MedicalDocumentController::class, 'store']);
    Route::get('/medical-documents/{medicalDocument}', [MedicalDocumentController::class, 'show']);
    Route::get('/medical-documents/{medicalDocument}/download', [MedicalDocumentController::class, 'download']);
    Route::get('/prescriptions', [PrescriptionController::class, 'index']);
    Route::post('/prescriptions', [PrescriptionController::class, 'store']);
    Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show']);
    Route::get(
        '/prescriptions/{prescription}/pdf',
        [PrescriptionPdfController::class, 'generate']
    );
    Route::get('/settings', [SettingController::class, 'show']);
    Route::put('/settings', [SettingController::class, 'update']);
    Route::get('/access-permissions', [AccessPermissionController::class, 'index']);
    Route::post('/access-permissions', [AccessPermissionController::class, 'store']);
    Route::patch('/access-permissions/{accessPermission}/revoke', [AccessPermissionController::class, 'revoke']);
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::get('/appointments/{appointment}', [AppointmentController::class, 'show']);
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus']);
    Route::get(
        '/doctor-schedules',
        [DoctorScheduleController::class, 'index']
    );

    Route::post(
        '/doctor-schedules',
        [DoctorScheduleController::class, 'store']
    );
    Route::get(
        '/doctors/{doctor}/availability',
        [DoctorAvailabilityController::class, 'show']
    );
    Route::get('/doctor-unavailabilities', [DoctorUnavailabilityController::class, 'index']);
    Route::post('/doctor-unavailabilities', [DoctorUnavailabilityController::class, 'store']);
    Route::patch('/doctor-unavailabilities/{doctorUnavailability}/cancel', [DoctorUnavailabilityController::class, 'cancel']);
});

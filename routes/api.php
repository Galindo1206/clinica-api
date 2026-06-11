<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\MedicalDocumentController;

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
});

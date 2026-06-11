<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMedicalDocumentRequest;
use App\Models\MedicalDocument;
use App\Services\AuditLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MedicalDocumentController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role?->name === 'patient') {
            $documents = MedicalDocument::where('patient_id', $user->patient->id)
                ->latest()
                ->get();
        } elseif ($user->role?->name === 'doctor') {
            $documents = MedicalDocument::latest()->get();
        } else {
            $documents = MedicalDocument::latest()->get();
        }

        return response()->json([
            'data' => $documents,
        ]);
    }

    public function store(StoreMedicalDocumentRequest $request)
    {
        $user = $request->user();

        if (! in_array($user->role?->name, ['doctor', 'admin'])) {
            return response()->json([
                'message' => 'No autorizado para subir documentos médicos.',
            ], 403);
        }

        $data = $request->validated();

        $file = $request->file('file');

        $path = $file->store('medical-documents', 'local');

        $document = MedicalDocument::create([
            'patient_id' => $data['patient_id'],
            'uploaded_by_user_id' => $user->id,
            'document_type' => $data['document_type'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_mime_type' => $file->getMimeType(),
            'file_size' => $file->getSize(),
            'document_date' => $data['document_date'] ?? null,
            'is_private' => true,
        ]);

        AuditLogService::record(
            userId: $user->id,
            patientId: $document->patient_id,
            action: 'upload_medical_document',
            module: 'medical_documents',
            description: 'Usuario subió un documento médico',
            request: $request
        );

        return response()->json([
            'message' => 'Documento médico subido correctamente',
            'data' => $document,
        ], 201);
    }

    public function show(Request $request, MedicalDocument $medicalDocument)
    {
        $user = $request->user();

        if ($user->role?->name === 'patient' && $medicalDocument->patient_id !== $user->patient->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        AuditLogService::record(
            userId: $user->id,
            patientId: $medicalDocument->patient_id,
            action: 'view_medical_document',
            module: 'medical_documents',
            description: 'Usuario visualizó un documento médico',
            request: $request
        );

        return response()->json([
            'data' => $medicalDocument,
        ]);
    }

    public function download(Request $request, MedicalDocument $medicalDocument)
    {
        $user = $request->user();

        if ($user->role?->name === 'patient' && $medicalDocument->patient_id !== $user->patient->id) {
            return response()->json(['message' => 'No autorizado.'], 403);
        }

        if (! Storage::disk('local')->exists($medicalDocument->file_path)) {
            return response()->json([
                'message' => 'Archivo no encontrado.',
            ], 404);
        }

        AuditLogService::record(
            userId: $user->id,
            patientId: $medicalDocument->patient_id,
            action: 'download_medical_document',
            module: 'medical_documents',
            description: 'Usuario descargó un documento médico',
            request: $request
        );

        return response()->download(
            storage_path('app/private/' . $medicalDocument->file_path),
            $medicalDocument->file_name
        );
    }
}

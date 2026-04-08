<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PrescriptionController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'obat_id' => 'required|exists:obat,id',
            'foto_resep' => 'required|image|max:5120',
        ]);

        try {
            $path = $request->file('foto_resep')->store('prescriptions', 'public');

            $prescription = Prescription::create([
                'user_id' => auth()->id(),
                'obat_id' => $request->obat_id,
                'foto_resep' => $path,
                'status' => 'menunggu',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Resep berhasil diupload, menunggu validasi apoteker.',
                'data' => $prescription->load('obat'),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal upload resep: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function validatePrescription($id)
    {
        $prescription = Prescription::findOrFail($id);

        $prescription->update([
            'status' => 'tervalidasi',
            'validated_by' => auth()->id(),
            'validated_at' => now(),
        ]);
        $this->notificationService->sendPrescriptionValidated($prescription);

        return response()->json([
            'success' => true,
            'message' => 'Resep berhasil divalidasi',
            'data' => $prescription,
        ]);
    }
}

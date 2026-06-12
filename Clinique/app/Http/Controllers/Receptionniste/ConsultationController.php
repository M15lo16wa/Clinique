<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Medecin;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    public function index(): JsonResponse
    {
        $consultations = Consultation::with([
            'patient:id,nom,prenom,telephone',
            'medecin:id,nom,prenom,specialite',
        ])->orderByDesc('date_consultation')->get();

        return response()->json(['consultations' => $consultations]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id'        => 'required|exists:patient,id',
            'medecin_id'        => 'required|exists:medecin,id',
            'date_consultation' => 'required|date',
            'motif'             => 'required|string',
            'observation'       => 'nullable|string',
        ]);

        $consultation = Consultation::create($request->only(
            'patient_id', 'medecin_id', 'date_consultation',
            'motif', 'observation'
        ));

        return response()->json([
            'message'      => 'Consultation enregistrée.',
            'consultation' => $consultation->load([
                'patient:id,nom,prenom,telephone',
                'medecin:id,nom,prenom,specialite',
            ]),
        ], 201);
    }

    public function show(Consultation $consultation): JsonResponse
    {
        return response()->json([
            'consultation' => $consultation->load([
                'patient:id,nom,prenom,telephone,adresse',
                'medecin:id,nom,prenom,specialite,telephone',
            ]),
        ]);
    }

    public function update(Request $request, Consultation $consultation): JsonResponse
    {
        $request->validate([
            'medecin_id'        => 'sometimes|exists:medecin,id',
            'date_consultation' => 'sometimes|date',
            'motif'             => 'sometimes|string',
            'diagnostic'        => 'sometimes|string',
            'observation'       => 'nullable|string',
        ]);

        $consultation->update($request->only(
            'medecin_id', 'date_consultation', 'motif', 'diagnostic', 'observation'
        ));

        return response()->json([
            'message'      => 'Consultation mise à jour.',
            'consultation' => $consultation->fresh()->load([
                'patient:id,nom,prenom,telephone',
                'medecin:id,nom,prenom,specialite',
            ]),
        ]);
    }

    public function destroy(Consultation $consultation): JsonResponse
    {
        $consultation->delete();

        return response()->json(['message' => 'Consultation supprimée.']);
    }

    public function historiquePatient(Patient $patient): JsonResponse
    {
        $consultations = $patient->consultations()
            ->with('medecin:id,nom,prenom,specialite')
            ->orderByDesc('date_consultation')
            ->get();

        return response()->json([
            'patient'       => $patient->only('id', 'nom', 'prenom'),
            'consultations' => $consultations,
        ]);
    }

    public function medecinsDisponibles(): JsonResponse
    {
        $medecins = Medecin::whereHas('utilisateur', fn ($q) => $q->where('is_active', true))
            ->get(['id', 'nom', 'prenom', 'specialite', 'telephone']);

        return response()->json(['medecins' => $medecins]);
    }
}

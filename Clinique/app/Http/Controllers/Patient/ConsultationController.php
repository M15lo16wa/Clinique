<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\ConsultationHistoryResource;
use App\Http\Resources\ConsultationDetailResource;


class ConsultationController extends Controller
{
    private function patientActuel(Request $request)
    {
        $patient = $request->user()->patient;

        if (! $patient) {
            abort(response()->json(['message' => 'Profil patient introuvable. Contactez l\'administrateur.'], 404));
        }

        return $patient;
    }

    // Historique de toutes ses consultations
    public function index(Request $request): JsonResponse
    {
        $patient = $this->patientActuel($request);

        $consultations = Consultation::where('patient_id', $patient->id)
            ->with('medecin:id,nom,prenom,specialite')
            ->orderByDesc('date_consultation')
            ->get();

        return response()->json([
            'consultations' => ConsultationHistoryResource::collection($consultations),
        ]);
    }

    // Détail d'une consultation (uniquement les siennes)
    public function show(Request $request, Consultation $consultation): JsonResponse
    {
        $patient = $this->patientActuel($request);

        if ($consultation->patient_id !== $patient->id) {
            return response()->json(['message' => 'Consultation non trouvée.'], 404);
        }

        return response()->json([
            'consultation' => new ConsultationDetailResource($consultation->load('medecin:id,nom,prenom,specialite')),
        ]);
    }
}

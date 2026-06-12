<?php

namespace App\Http\Controllers\Medecin;

use App\Http\Controllers\Controller;
use App\Models\Consultation;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsultationController extends Controller
{
    private function medecinActuel(Request $request)
    {
        $medecin = $request->user()->medecin;

        if (! $medecin) {
            abort(response()->json(['message' => 'Profil médecin introuvable. Contactez l\'administrateur.'], 404));
        }

        return $medecin;
    }

    // Liste de toutes les consultations du médecin connecté
    public function index(Request $request): JsonResponse
    {
        $medecin = $this->medecinActuel($request);

        $consultations = Consultation::where('medecin_id', $medecin->id)
            ->with('patient:id,nom,prenom,sexe,date_naissance,telephone')
            ->orderByDesc('date_consultation')
            ->get();

        return response()->json([
            'medecin'       => $medecin->only('id', 'nom', 'prenom', 'specialite'),
            'consultations' => $consultations,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'patient_id'        => 'required|exists:patient,id',
            'medecin_id'        => 'required|exists:medecin,id',
            'date_consultation' => 'required|date',
            'motif'             => 'required|string',
            'observation'       => 'nullable|string',
            'diagnostic'        => 'required|string'
            
        ]);

        $consultation = Consultation::create($request->only(
            'patient_id', 'medecin_id', 'date_consultation',
            'motif', 'diagnostic', 'observation'
        ));

        return response()->json([
            'message'      => 'Consultation enregistrée.',
            'consultation' => $consultation->load([
                'patient:id,nom,prenom,telephone',
                'medecin:id,nom,prenom,specialite',
            ]),
        ], 201);
    }

    // Détail d'une consultation (uniquement les siennes)
    public function show(Request $request, Consultation $consultation): JsonResponse
    {
        $medecin = $this->medecinActuel($request);

        if ($consultation->medecin_id !== $medecin->id) {
            return response()->json(['message' => 'Consultation non trouvée.'], 404);
        }

        return response()->json([
            'consultation' => $consultation->load('patient'),
        ]);
    }

    // Mettre à jour le diagnostic et l'observation d'une consultation
    public function update(Request $request, Consultation $consultation): JsonResponse
    {
        $medecin = $this->medecinActuel($request);

        if ($consultation->medecin_id !== $medecin->id) {
            return response()->json(['message' => 'Consultation non trouvée.'], 404);
        }

        $request->validate([
            'diagnostic'  => 'sometimes|string',
            'observation' => 'nullable|string',
        ]);

        $consultation->update($request->only('diagnostic', 'observation'));

        return response()->json([
            'message'      => 'Consultation mise à jour.',
            'consultation' => $consultation->fresh()->load('patient:id,nom,prenom'),
        ]);
    }

    // Historique de toutes les consultations d'un patient
    public function historiquePatient(Request $request, Patient $patient): JsonResponse
    {
        $consultations = Consultation::where('patient_id', $patient->id)
            ->with('medecin:id,nom,prenom,specialite')
            ->orderByDesc('date_consultation')
            ->get();

        return response()->json([
            'patient' => $patient->only('id', 'nom', 'prenom', 'sexe', 'date_naissance', 'adresse', 'telephone'),
            'consultations' => $consultations,
        ]);
    }

    // Profil complet d'un patient (dossier médical)
    public function dossierPatient(Request $request, Patient $patient): JsonResponse
    {
        $medecin = $this->medecinActuel($request);

        $aConsulte = Consultation::where('patient_id', $patient->id)
            ->where('medecin_id', $medecin->id)
            ->exists();

        if (! $aConsulte) {
            return response()->json(['message' => 'Accès refusé. Ce patient ne vous a pas été assigné.'], 403);
        }

        $patient->load([
            'consultations' => fn ($q) => $q->orderByDesc('date_consultation'),
            'consultations.medecin:id,nom,prenom,specialite',
        ]);

        return response()->json(['dossier' => $patient]);
    }
}

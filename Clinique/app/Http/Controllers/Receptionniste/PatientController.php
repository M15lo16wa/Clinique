<?php

namespace App\Http\Controllers\Receptionniste;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Models\Patient;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function index(): JsonResponse
    {
        $patients = Patient::with('utilisateur:id,email,is_active')
            ->orderBy('nom')
            ->get();

        return response()->json(['patients' => $patients]);
    }

public function store(Request $request): JsonResponse
{
    $request->validate([
        'nom'            => 'required|string|max:100',
        'prenom'         => 'required|string|max:100',
        'sexe'           => 'required|in:M,F',
        'date_naissance' => 'required|date|before:today',
        'telephone'      => 'nullable|string|max:20',
        'adresse'        => 'required|string|max:255',

        'email'          => 'required|email|unique:utilisateurs,email',
        'password'       => 'required|string|min:6',
    ]);

    $patient = DB::transaction(function () use ($request) {
        $utilisateur = Utilisateur::create([
            'email'    => $request->email,
            'password' => Hash::make($request->password), 
            'role'     => 'patient',
        ]);

        return Patient::create([
            'utilisateur_id' => $utilisateur->id,
            'nom'            => $request->nom,
            'prenom'         => $request->prenom,
            'sexe'           => $request->sexe,
            'date_naissance' => $request->date_naissance,
            'telephone'      => $request->telephone,
            'adresse'        => $request->adresse,
        ]);
    });

    return response()->json([
        'message' => 'Patient enregistré avec succès.',
        'patient' => $patient->load('utilisateur:id,email,is_active'),
    ], 201);
}

    public function show(Patient $patient): JsonResponse
    {
        return response()->json([
            'patient' => $patient->load('utilisateur:id,email,is_active'),
        ]);
    }

    public function update(Request $request, Patient $patient): JsonResponse
    {
        $request->validate([
            'nom'            => 'sometimes|string|max:100',
            'prenom'         => 'sometimes|string|max:100',
            'sexe'           => 'sometimes|in:M,F',
            'date_naissance' => 'sometimes|date|before:today',
            'telephone'      => 'nullable|string|max:20',
            'adresse'        => 'sometimes|string|max:255',
            'email'          => 'sometimes|email|unique:utilisateurs,email,' . $patient->utilisateur_id,
        ]);

        DB::transaction(function () use ($request, $patient) {
            $patient->update($request->only(
                'nom', 'prenom', 'sexe', 'date_naissance', 'telephone', 'adresse'
            ));

            if ($request->has('email')) {
                $patient->utilisateur->update(['email' => $request->email]);
            }
        });

        return response()->json([
            'message' => 'Patient mis à jour.',
            'patient' => $patient->fresh()->load('utilisateur:id,email,is_active'),
        ]);
    }

    public function destroy(Patient $patient): JsonResponse
    {
        DB::transaction(function () use ($patient) {
            $utilisateur = $patient->utilisateur;
            $patient->delete();
            $utilisateur->delete();
        });

        return response()->json(['message' => 'Patient supprimé.']);
    }

    public function search(Request $request): JsonResponse
    {
        $request->validate(['q' => 'required|string|min:2']);

        $terme = $request->q;

        $patients = Patient::with('utilisateur:id,email,is_active')
            ->where('nom', 'like', "%{$terme}%")
            ->orWhere('prenom', 'like', "%{$terme}%")
            ->orWhere('telephone', 'like', "%{$terme}%")
            ->orderBy('nom')
            ->get();

        return response()->json(['patients' => $patients]);
    }
}

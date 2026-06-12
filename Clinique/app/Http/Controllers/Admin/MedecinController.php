<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Medecin;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MedecinController extends Controller
{
    public function index(): JsonResponse
    {
        $medecins = Medecin::with('utilisateur:id,email,is_active')->get();

        return response()->json(['medecins' => $medecins]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email'      => 'required|email|unique:utilisateurs,email',
            'password'   => 'required|string|min:6',
            'nom'        => 'required|string|max:100',
            'prenom'     => 'required|string|max:100',
            'specialite' => 'required|string|max:100',
            'telephone'  => 'required|string|max:20',
        ]);

        $medecin = DB::transaction(function () use ($request) {
            $utilisateur = Utilisateur::create([
                'email'    => $request->email,
                'password' => $request->password,
                'role'     => 'medecin',
            ]);

            return Medecin::create([
                'utilisateur_id' => $utilisateur->id,
                'nom'            => $request->nom,
                'prenom'         => $request->prenom,
                'specialite'     => $request->specialite,
                'telephone'      => $request->telephone,
            ]);
        });

        return response()->json([
            'message' => 'Médecin créé avec succès.',
            'medecin' => $medecin->load('utilisateur:id,email,is_active'),
        ], 201);
    }

    public function show(Medecin $medecin): JsonResponse
    {
        return response()->json([
            'medecin' => $medecin->load('utilisateur:id,email,is_active'),
        ]);
    }

    public function update(Request $request, Medecin $medecin): JsonResponse
    {
        $request->validate([
            'nom'        => 'sometimes|string|max:100',
            'prenom'     => 'sometimes|string|max:100',
            'specialite' => 'sometimes|string|max:100',
            'telephone'  => 'sometimes|string|max:20',
            'email'      => 'sometimes|email|unique:utilisateurs,email,' . $medecin->utilisateur_id,
        ]);

        DB::transaction(function () use ($request, $medecin) {
            $medecin->update($request->only('nom', 'prenom', 'specialite', 'telephone'));

            if ($request->has('email')) {
                $medecin->utilisateur->update(['email' => $request->email]);
            }
        });

        return response()->json([
            'message' => 'Médecin mis à jour.',
            'medecin' => $medecin->fresh()->load('utilisateur:id,email,is_active'),
        ]);
    }

    public function destroy(Medecin $medecin): JsonResponse
    {
        DB::transaction(function () use ($medecin) {
            $utilisateur = $medecin->utilisateur;
            $medecin->delete();
            $utilisateur->delete();
        });

        return response()->json(['message' => 'Médecin supprimé.']);
    }

    public function toggleActive(Medecin $medecin): JsonResponse
    {
        $utilisateur = $medecin->utilisateur;
        $utilisateur->update(['is_active' => ! $utilisateur->is_active]);

        $etat = $utilisateur->is_active ? 'activé' : 'désactivé';

        return response()->json([
            'message' => "Compte médecin {$etat}.",
            'medecin' => $medecin->load('utilisateur:id,email,is_active'),
        ]);
    }
}

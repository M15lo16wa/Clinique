<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Receptionniste;
use App\Models\Utilisateur;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReceptionnisteController extends Controller
{
    public function index(): JsonResponse
    {
        $receptionistes = Receptionniste::with('utilisateur:id,email,is_active')->get();

        return response()->json(['receptionistes' => $receptionistes]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email|unique:utilisateurs,email',
            'password' => 'required|string|min:6',
            'nom'      => 'required|string|max:100',
            'prenom'   => 'required|string|max:100',
        ]);

        $receptionniste = DB::transaction(function () use ($request) {
            $utilisateur = Utilisateur::create([
                'email'    => $request->email,
                'password' => $request->password,
                'role'     => 'receptionniste',
            ]);

            return Receptionniste::create([
                'utilisateur_id' => $utilisateur->id,
                'nom'            => $request->nom,
                'prenom'         => $request->prenom,
            ]);
        });

        return response()->json([
            'message'         => 'Réceptionniste créé avec succès.',
            'receptionniste'  => $receptionniste->load('utilisateur:id,email,is_active'),
        ], 201);
    }

    public function show(Receptionniste $receptionniste): JsonResponse
    {
        return response()->json([
            'receptionniste' => $receptionniste->load('utilisateur:id,email,is_active'),
        ]);
    }

    public function update(Request $request, Receptionniste $receptionniste): JsonResponse
    {
        $request->validate([
            'nom'    => 'sometimes|string|max:100',
            'prenom' => 'sometimes|string|max:100',
            'email'  => 'sometimes|email|unique:utilisateurs,email,' . $receptionniste->utilisateur_id,
        ]);

        DB::transaction(function () use ($request, $receptionniste) {
            $receptionniste->update($request->only('nom', 'prenom'));

            if ($request->has('email')) {
                $receptionniste->utilisateur->update(['email' => $request->email]);
            }
        });

        return response()->json([
            'message'        => 'Réceptionniste mis à jour.',
            'receptionniste' => $receptionniste->fresh()->load('utilisateur:id,email,is_active'),
        ]);
    }

    public function destroy(Receptionniste $receptionniste): JsonResponse
    {
        DB::transaction(function () use ($receptionniste) {
            $utilisateur = $receptionniste->utilisateur;
            $receptionniste->delete();
            $utilisateur->delete();
        });

        return response()->json(['message' => 'Réceptionniste supprimé.']);
    }

    public function toggleActive(Receptionniste $receptionniste): JsonResponse
    {
        $utilisateur = $receptionniste->utilisateur;
        $utilisateur->update(['is_active' => ! $utilisateur->is_active]);

        $etat = $utilisateur->is_active ? 'activé' : 'désactivé';

        return response()->json([
            'message'        => "Compte réceptionniste {$etat}.",
            'receptionniste' => $receptionniste->load('utilisateur:id,email,is_active'),
        ]);
    }
}

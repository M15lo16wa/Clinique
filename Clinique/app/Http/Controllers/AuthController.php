<?php

namespace App\Http\Controllers;

use App\Models\Utilisateur;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // creation de compte utilisateur (par defaut patient)
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'          => 'required|email|unique:utilisateurs,email',
            'password'       => 'required|string|min:6',
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'sexe'           => 'required|in:M,F',
            'date_naissance' => 'required|date|before:today',
            'telephone'      => 'nullable|string|max:20',
            'adresse'        => 'required|string|max:255',
        ],[
            'email.unique' => 'Un compte existe déjà avec cet email. Veuillez vous connecter ou réinitialiser votre mot de passe.',
        ]);

        $patient = DB::transaction(function () use ($validated) {
            $utilisateur = Utilisateur::create([
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role'     => 'patient',
            ]);

            return Patient::create([
                'utilisateur_id' => $utilisateur->id,
                'nom'            => $validated['nom'],
                'prenom'         => $validated['prenom'],
                'sexe'           => $validated['sexe'],
                'date_naissance' => $validated['date_naissance'],
                'telephone'      => $validated['telephone'] ?? null,
                'adresse'        => $validated['adresse'],
            ]);
        });

        $token = $patient->utilisateur->createToken('api-token')->plainTextToken;

        return response()->json([
            'message'     => 'Compte créé avec succès.',
            'utilisateur' => $patient->utilisateur,
            'patient'     => $patient,
            'token'       => $token,
        ], 201);
    }   

    // Demande de réinitialisation du mot de passe (envoie du token)
    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => 'required|email']);

        $user = Utilisateur::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Aucun compte associé à cet email.'
            ], 404);
        }

        // Génère un token (stocké automatiquement dans password_reset_tokens)
        $token = Password::createToken($user);

        // En production, on enverrait un email. Ici on simule.
        return response()->json([
            'message' => 'Utilisez ce code/token pour réinitialiser votre mot de passe.',
            'token'   => $token,
        ]);
    }

    // Reinitialisation du mot de passe avec le token reçu
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Mot de passe réinitialisé avec succès.'])
            : response()->json(['email' => [__($status)]], 422);
    }

    // creation de compte utilisateur (par defaut administrateur)
    public function registerAdmin(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email'    => 'required|email|unique:utilisateurs,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin',
        ]);

        $utilisateur = Utilisateur::create($validated);

        $token = $utilisateur->createToken('api-token')->plainTextToken;

        return response()->json([
            'message'      => 'Compte créé avec succès.',
            'utilisateur'  => $utilisateur,
            'token'        => $token,
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $utilisateur = Utilisateur::where('email', $request->email)->first();

        if (! $utilisateur || ! Hash::check($request->password, $utilisateur->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email ou mot de passe incorrect.'],
            ]);
        }

        $utilisateur->tokens()->delete();

        $token = $utilisateur->createToken('api-token')->plainTextToken;

        return response()->json([
            'message'     => 'Connexion réussie.',
            'utilisateur' => $utilisateur,
            'token'       => $token,
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Déconnexion réussie.']);
    }
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();
        $patient = $user->patient;

        return response()->json([
            'utilisateur' => $user,
            'patient'     => $patient,
        ]);
    }
}

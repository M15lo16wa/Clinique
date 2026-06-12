<?php

use App\Http\Controllers\Admin\MedecinController as AdminMedecinController;
use App\Http\Controllers\Admin\ReceptionnisteController as AdminReceptionnisteController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Medecin\ConsultationController as MedecinConsultationController;
use App\Http\Controllers\Patient\ConsultationController as PatientConsultationController;
use App\Http\Controllers\Patient\ProfileController as PatientProfileController;
use App\Http\Controllers\Receptionniste\ConsultationController;
use App\Http\Controllers\Receptionniste\PatientController;
use Illuminate\Support\Facades\Route;

// Routes publiques
Route::prefix('auth')->group(function () {
    Route::post('/login',    [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
// routes de renitialisation de mot de passe
    Route::post('/forgotpassword', [AuthController::class, 'forgotPassword']);
    Route::post('/resetpassword', [AuthController::class, 'resetPassword']);
});

// Routes protégées (token requis)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);

    // ─── Routes Admin ────────────────────────────────────────────────────────
    Route::middleware('admin')->prefix('admin')->group(function () {

    // route pour la creation de compte administrateur
        Route::post('/registers', [AuthController::class, 'registerAdmin'])->middleware('admin');

        // Gestion des médecins
        Route::get('/medecins',                    [AdminMedecinController::class, 'index'])->middleware('admin');
        Route::post('/medecins',                   [AdminMedecinController::class, 'store'])->middleware('admin');
        Route::get('/medecins/{medecin}',          [AdminMedecinController::class, 'show'])->middleware('admin');
        Route::put('/medecins/{medecin}',          [AdminMedecinController::class, 'update'])->middleware('admin');
        Route::delete('/medecins/{medecin}',       [AdminMedecinController::class, 'destroy'])->middleware('admin');
        Route::patch('/medecins/{medecin}/toggle', [AdminMedecinController::class, 'toggleActive'])->middleware('admin');

        // Gestion des réceptionnistes
        Route::get('/receptionistes',                           [AdminReceptionnisteController::class, 'index'])->middleware('admin');
        Route::post('/receptionistes',                          [AdminReceptionnisteController::class, 'store'])->middleware('admin');
        Route::get('/receptionistes/{receptionniste}',          [AdminReceptionnisteController::class, 'show'])->middleware('admin');
        Route::put('/receptionistes/{receptionniste}',          [AdminReceptionnisteController::class, 'update'])->middleware('admin');
        Route::delete('/receptionistes/{receptionniste}',       [AdminReceptionnisteController::class, 'destroy'])->middleware('admin');
        Route::patch('/receptionistes/{receptionniste}/toggle', [AdminReceptionnisteController::class, 'toggleActive'])->middleware('admin');
    });

    // ─── Routes Réceptionniste ────────────────────────────────────────────────
    Route::middleware('receptionniste')->prefix('receptionniste')->group(function () {

        // Gestion des patients
        Route::get('/patients',                [PatientController::class, 'index'])->middleware('receptionniste');
        Route::post('/patients',               [PatientController::class, 'store'])->middleware('receptionniste');
        Route::get('/patients/search',         [PatientController::class, 'search'])->middleware('receptionniste');
        Route::get('/patients/{patient}',      [PatientController::class, 'show'])->middleware('receptionniste');
        Route::put('/patients/{patient}',      [PatientController::class, 'update'])->middleware('receptionniste');
        Route::delete('/patients/{patient}',   [PatientController::class, 'destroy'])->middleware('receptionniste');

        // Gestion des consultations
        Route::get('/consultations',                              [ConsultationController::class, 'index'])->middleware('receptionniste');
        Route::post('/consultations',                             [ConsultationController::class, 'store'])->middleware('receptionniste');
        Route::get('/consultations/{consultation}',               [ConsultationController::class, 'show'])->middleware('receptionniste');
        Route::put('/consultations/{consultation}',               [ConsultationController::class, 'update'])->middleware('receptionniste');
        Route::delete('/consultations/{consultation}',            [ConsultationController::class, 'destroy'])->middleware('receptionniste');
        Route::get('/patients/{patient}/historique',              [ConsultationController::class, 'historiquePatient'])->middleware('receptionniste');
        Route::get('/medecins-disponibles',                       [ConsultationController::class, 'medecinsDisponibles'])->middleware('receptionniste');
    });

    // ─── Routes Médecin ───────────────────────────────────────────────────────
    Route::middleware('medecin')->prefix('medecin')->group(function () {

        // Mes consultations
        Route::get('/consultations',                         [MedecinConsultationController::class, 'index'])->middleware('medecin');
        Route::get('/consultations/{consultation}',          [MedecinConsultationController::class, 'show'])->middleware('medecin');
        Route::post('/consultations',                             [ConsultationController::class, 'store'])->middleware('medecin');
        Route::put('/consultations/{consultation}',          [MedecinConsultationController::class, 'update'])->middleware('medecin');

        // Dossiers patients
        Route::get('/patients/{patient}/historique',         [MedecinConsultationController::class, 'historiquePatient'])->middleware('medecin');
        Route::get('/patients/{patient}/dossier',            [MedecinConsultationController::class, 'dossierPatient'])->middleware('medecin');
    });

    // ─── Routes Patient ───────────────────────────────────────────────────────
    Route::middleware('patient')->prefix('patient')->group(function () {

        // Profil
        Route::get('/profil',  [PatientProfileController::class, 'show']);
        Route::put('/profil',  [PatientProfileController::class, 'update']);

        // Consultations (résultats / historique)
        Route::get('/consultations',               [PatientConsultationController::class, 'index']);
        Route::get('/consultations/{consultation}', [PatientConsultationController::class, 'show']);
    });
});

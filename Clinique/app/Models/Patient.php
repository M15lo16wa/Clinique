<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patient';

    protected $fillable = [
        'utilisateur_id',
        'nom',
        'prenom',
        'sexe',
        'date_naissance',
        'telephone',
        'adresse',
    ];

    protected function casts(): array
    {
        return [
            'date_naissance' => 'date',
        ];
    }

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'patient_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Medecin extends Model
{
    protected $table = 'medecin';

    protected $fillable = [
        'utilisateur_id',
        'nom',
        'prenom',
        'specialite',
        'telephone',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'medecin_id');
    }
}

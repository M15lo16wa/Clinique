<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Receptionniste extends Model
{
    protected $table = 'receptionniste';

    protected $fillable = [
        'utilisateur_id',
        'nom',
        'prenom',
    ];

    public function utilisateur()
    {
        return $this->belongsTo(Utilisateur::class, 'utilisateur_id');
    }
}

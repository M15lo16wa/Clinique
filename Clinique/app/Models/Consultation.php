<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consultation extends Model
{
    protected $table = 'consultation';

    protected $fillable = [
        'patient_id',
        'medecin_id',
        'date_consultation',
        'motif',
        'diagnostic',
        'observation',
    ];

    protected function casts(): array
    {
        return [
            'date_consultation' => 'date',
        ];
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'patient_id');
    }

    public function medecin()
    {
        return $this->belongsTo(Medecin::class, 'medecin_id');
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationHistoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'date_consultation' => $this->date_consultation,
            'motif'             => $this->motif,
            'medecin'           => [
                'id'         => $this->medecin->id,
                'nom'        => $this->medecin->nom,
                'prenom'     => $this->medecin->prenom,
                'specialite' => $this->medecin->specialite,
            ],
        ];
    }
}

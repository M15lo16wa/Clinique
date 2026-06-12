<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ConsultationDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'date_consultation' => $this->date_consultation,
            'motif'             => $this->motif,
            'diagnostic'        => $this->diagnostic,
            'observation'       => $this->observation,
            
        ];
    }
}

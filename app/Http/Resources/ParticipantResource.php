<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nom' => $this->nom,
            'cognom' => $this->cognom,
            'nom_complet' => $this->nom . ' ' . $this->cognom,
            'email' => $this->email,
            'tipus_entrada' => $this->tipus_entrada,
            'necessitats_especials' => $this->necessitats_especials,
            'registrat_el' => $this->created_at->format('d/m/Y H:i'),
        ];
    }
}
<?php

namespace Database\Seeders;

use App\Models\Participant;
use Illuminate\Database\Seeder;

class ParticipantSeeder extends Seeder
{
    public function run(): void
    {
        $participants = [
            ['nom' => 'Marta', 'cognom' => 'Puig', 'email' => 'marta@hackaccess.dev', 'tipus_entrada' => 'Presencial', 'necessitats_especials' => false],
            ['nom' => 'Joan', 'cognom' => 'Ferrer', 'email' => 'joan@hackaccess.dev', 'tipus_entrada' => 'Online', 'necessitats_especials' => false],
            ['nom' => 'Laia', 'cognom' => 'Vidal', 'email' => 'laia@hackaccess.dev', 'tipus_entrada' => 'Presencial', 'necessitats_especials' => true],
            ['nom' => 'Arnau', 'cognom' => 'Costa', 'email' => 'arnau@hackaccess.dev', 'tipus_entrada' => 'Mentor', 'necessitats_especials' => false],
            ['nom' => 'Nuria', 'cognom' => 'Soler', 'email' => 'nuria@hackaccess.dev', 'tipus_entrada' => 'Online', 'necessitats_especials' => true],
        ];

        foreach ($participants as $p) {
            Participant::create($p);
        }
    }
}
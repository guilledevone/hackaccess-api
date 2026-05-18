<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ParticipantResource;
use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    public function index()
    {
        $participants = Participant::orderBy('created_at', 'desc')->get();
        return ParticipantResource::collection($participants);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => 'required|string|min:2|max:100',
            'cognom' => 'required|string|min:2|max:100',
            'email' => 'required|email|unique:participants,email',
            'tipus_entrada' => 'required|in:Presencial,Online,Mentor',
            'necessitats_especials' => 'boolean',
        ]);

        $participant = Participant::create($validated);

        return (new ParticipantResource($participant))
            ->response()
            ->setStatusCode(201);
    }

    public function destroy(Participant $participant)
    {
        $participant->delete();
        return response()->json(['message' => 'Participant eliminat correctament.'], 200);
    }
}
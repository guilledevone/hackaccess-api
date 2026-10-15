<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ParticipantResource;
use App\Models\Participant;
use Illuminate\Http\Request;

class ParticipantController extends Controller
{
    private const ADMIN_EMAIL = 'admin@hackaccess.dev';


    private function isAdmin(Request $request): bool
    {
        return $request->user()?->email === self::ADMIN_EMAIL;
    }

    private function forbiddenResponse()
    {
        return response()->json([
            'message' => 'No tens permisos per realitzar aquesta acció.'
        ], 403);
    }


    public function index()
    {
        $participants = Participant::orderBy('created_at', 'desc')->get();
        return ParticipantResource::collection($participants);
    }

    public function show(Participant $participant)
    {
        return new ParticipantResource($participant);
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

    public function update(Request $request, Participant $participant)
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse();
        }

        $validated = $request->validate([
            'nom' => 'sometimes|required|string|min:2|max:100',
            'cognom' => 'sometimes|required|string|min:2|max:100',
            'email' => 'sometimes|required|email|unique:participants,email,' . $participant->id,
            'tipus_entrada' => 'sometimes|required|in:Presencial,Online,Mentor',
            'necessitats_especials' => 'sometimes|boolean',
        ]);

        $participant->update($validated);

        return new ParticipantResource($participant);
    }

    public function destroy(Request $request, Participant $participant)
    {
        if (!$this->isAdmin($request)) {
            return $this->forbiddenResponse();
        }

        $participant->delete();
        return response()->json(['message' => 'Participant eliminat correctament.'], 200);
    }
}
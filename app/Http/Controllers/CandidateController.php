<?php

namespace App\Http\Controllers;

use App\Candidates\CreateCandidate\CreateCandidateHandler;
use App\Candidates\CreateCandidate\CreateCandidateRequest;
use Illuminate\Http\JsonResponse;

class CandidateController extends Controller
{
    public function store(CreateCandidateRequest $request, CreateCandidateHandler $handler): JsonResponse
    {
        $candidate = $handler->handle($request->toCommand());

        return response()->json([
            'candidate' => $candidate,
            'message' => 'Candidate created successfully.',
        ], JsonResponse::HTTP_CREATED);
    }
}

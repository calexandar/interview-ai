<?php

namespace App\Http\Controllers;

use App\Interviewing\CreateInterview\CreateInterviewHandler;
use App\Interviewing\CreateInterview\CreateInterviewRequest;
use Illuminate\Http\JsonResponse;

class InterviewController extends Controller
{
    public function store(CreateInterviewRequest $request, CreateInterviewHandler $handler): JsonResponse
    {
        $interview = $handler->handle($request->toCommand());

        return response()->json([
            'interview' => $interview,
            'message' => 'Interview created successfully.',
        ], JsonResponse::HTTP_CREATED);
    }
}

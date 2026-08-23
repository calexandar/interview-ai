<?php

namespace App\Interviewing\CreateInterview;

use App\Http\Controllers\Controller;
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

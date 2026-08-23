<?php

namespace App\Positions\CreatePosition;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class PositionController extends Controller
{
    public function store(CreatePositionRequest $request, CreatePositionHandler $handler): JsonResponse
    {
        $position = $handler->handle($request->toCommand());

        return response()->json([
            'position' => $position,
            'message' => 'Position created successfully.',
        ], JsonResponse::HTTP_CREATED);
    }
}

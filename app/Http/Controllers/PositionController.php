<?php

namespace App\Http\Controllers;

use App\Positions\CreatePosition\CreatePositionHandler;
use App\Positions\CreatePosition\CreatePositionRequest;
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

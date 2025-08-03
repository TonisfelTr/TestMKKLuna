<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;

class ActivityController extends Controller
{
    #[Get(
        path: '/api/activities',
        summary: 'Получить все корневые виды деятельности',
        security: [['ApiKeyAuth' => []]],
        tags: ['Activities'],
        responses: [
            new Response(response: 200, description: 'Список корневых видов деятельности')
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(Activity::with('children')->whereNull('parent_id')->get());
    }

    #[Get(
        path: '/api/activities/{id}',
        summary: 'Получить вид деятельности по ID',
        security: [['ApiKeyAuth' => []]],
        tags: ['Activities'],
        parameters: [
            new Parameter(name: 'id', in: 'path', required: true, description: 'ID вида деятельности', schema: new Schema(type: 'integer'))
        ],
        responses: [
            new Response(response: 200, description: 'Информация о виде деятельности')
        ]
    )]
    public function show($id): JsonResponse
    {
        return response()->json(Activity::with('children')->findOrFail($id));
    }
}

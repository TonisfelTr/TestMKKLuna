<?php

namespace App\Http\Controllers;

use App\Models\Building;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Items;
use OpenApi\Attributes\JsonContent;
use OpenApi\Attributes\Property;
use OpenApi\Attributes\Response;

class BuildingController extends Controller
{
    #[Get(
        path: '/api/buildings',
        summary: 'Получить список всех зданий',
        security: [['ApiKeyAuth' => []]],
        tags: ['Buildings'],
        responses: [
            new Response(
                response: 200,
                description: 'Список зданий',
                content: new JsonContent(
                    type: 'array',
                    items: new Items(
                        properties: [
                            new Property(property: 'id', type: 'integer', example: 1),
                            new Property(property: 'address', type: 'string', example: 'г. Москва, ул. Ленина, 1'),
                            new Property(property: 'latitude', type: 'number', format: 'float', example: 55.7558),
                            new Property(property: 'longitude', type: 'number', format: 'float', example: 37.6173),
                            new Property(property: 'created_at', type: 'string', format: 'date-time'),
                            new Property(property: 'updated_at', type: 'string', format: 'date-time'),
                        ],
                        type: 'object'
                )
            ))
        ]
    )]
    public function index(): JsonResponse
    {
        return response()->json(Building::all());
    }
}

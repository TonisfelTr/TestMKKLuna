<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Building;
use App\Models\Organization;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes\Get;
use OpenApi\Attributes\Parameter;
use OpenApi\Attributes\Response;
use OpenApi\Attributes\Schema;

class OrganizationController extends Controller
{
    protected function getActivityWithChildrenIds($id, $depth = 3): array
    {
        $result = [$id];
        $children = Activity::where('parent_id', $id)->get();
        if ($depth <= 1) return $result;

        foreach ($children as $child) {
            $result = array_merge($result, $this->getActivityWithChildrenIds($child->id, $depth - 1));
        }

        return $result;
    }

    #[Get(
        path: '/api/buildings/{id}/organizations',
        summary: 'Получить организации в здании по ID',
        security: [['ApiKeyAuth' => []]],
        tags: ['Organizations'],
        parameters: [
            new Parameter(name: 'id', in: 'path', required: true, description: 'ID здания', schema: new Schema(type: 'integer'))
        ],
        responses: [
            new Response(response: 200, description: 'Список организаций')
        ]
    )]
    public function byBuilding($id): JsonResponse
    {
        $building = Building::with('organizations')->findOrFail($id);
        return response()->json($building->organizations);
    }

    #[Get(
        path: '/api/activities/{id}/organizations',
        summary: 'Получить организации по ID вида деятельности',
        security: [['ApiKeyAuth' => []]],
        tags: ['Organizations'],
        parameters: [
            new Parameter(name: 'id', in: 'path', required: true, description: 'ID вида деятельности', schema: new Schema(type: 'integer'))
        ],
        responses: [
            new Response(response: 200, description: 'Список организаций')
        ]
    )]
    public function byActivity($id): JsonResponse
    {
        $activityIds = $this->getActivityWithChildrenIds($id);
        $orgs = Organization::whereHas('activities', fn($q) => $q->whereIn('activities.id', $activityIds))->get();
        return response()->json($orgs);
    }

    #[Get(
        path: '/api/organizations/search',
        summary: 'Поиск организаций по названию или виду деятельности',
        security: [['ApiKeyAuth' => []]],
        tags: ['Organizations'],
        parameters: [
            new Parameter(name: 'name', in: 'query', required: false, description: 'Название организации', schema: new Schema(type: 'string')),
            new Parameter(name: 'activity', in: 'query', required: false, description: 'Название вида деятельности', schema: new Schema(type: 'string'))
        ],
        responses: [
            new Response(response: 200, description: 'Список организаций')
        ]
    )]
    public function search(Request $request): JsonResponse
    {
        if ($request->has('name')) {
            $orgs = Organization::where('name', 'like', '%' . $request->name . '%')->get();
        } elseif ($request->has('activity')) {
            $activity = Activity::where('name', $request->activity)->firstOrFail();
            $ids = $this->getActivityWithChildrenIds($activity->id);
            $orgs = Organization::whereHas('activities', fn($q) => $q->whereIn('activities.id', $ids))->get();
        } else {
            $orgs = [];
        }

        return response()->json($orgs);
    }

    #[Get(
        path: '/api/organizations/{id}',
        summary: 'Получить информацию об организации по ID',
        security: [['ApiKeyAuth' => []]],
        tags: ['Organizations'],
        parameters: [
            new Parameter(name: 'id', in: 'path', required: true, description: 'ID организации', schema: new Schema(type: 'integer'))
        ],
        responses: [
            new Response(response: 200, description: 'Информация об организации')
        ]
    )]
    public function show($id): JsonResponse
    {
        return response()->json(
            Organization::with(['phones', 'building', 'activities'])->findOrFail($id)
        );
    }

    #[Get(
        path: '/api/organizations/nearby',
        summary: 'Найти организации рядом по координатам',
        security: [['ApiKeyAuth' => []]],
        tags: ['Organizations'],
        parameters: [
            new Parameter(name: 'lat', in: 'query', required: true, description: 'Широта', schema: new Schema(type: 'number')),
            new Parameter(name: 'lng', in: 'query', required: true, description: 'Долгота', schema: new Schema(type: 'number')),
            new Parameter(name: 'radius', in: 'query', required: false, description: 'Радиус в метрах (по умолчанию 100)', schema: new Schema(type: 'number'))
        ],
        responses: [
            new Response(response: 200, description: 'Список ближайших организаций')
        ]
    )]
    public function nearby(Request $request): JsonResponse
    {
        $lat = floatval($request->lat);
        $lng = floatval($request->lng);
        $radius = floatval($request->radius ?? 100); // радиус в метрах

        $earthRadius = 6371000; // радиус Земли в метрах

        $buildings = Building::selectRaw("
        *,
        (
            {$earthRadius} * acos(
                cos(radians(?)) * cos(radians(latitude)) *
                cos(radians(longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(latitude))
            )
        ) as distance
    ", [$lat, $lng, $lat])
            ->orderBy('distance')
            ->get()
            ->filter(fn ($building) => $building->distance <= $radius)
            ->values(); // сбрасываем ключи, если нужно

        $orgs = Organization::whereIn('building_id', $buildings->pluck('id'))
            ->with(['building', 'activities', 'phones'])
            ->get();

        return response()->json($orgs);
    }
}

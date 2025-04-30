<?php

namespace App\Http\Controllers;

use App\Actions\Tariffs\CreateTariff;
use App\Actions\Tariffs\GetTariffs;
use App\Actions\Tariffs\UpdateTariff;
use App\DTOs\Tariffs\TariffIndexDTO;
use App\DTOs\Tariffs\TariffStoreDTO;
use App\DTOs\Tariffs\TariffUpdateDTO;
use App\Http\Requests\Tariffs\TariffDeleteRequest;
use App\Http\Requests\Tariffs\TariffIndexRequest;
use App\Http\Requests\Tariffs\TariffStoreRequest;
use App\Http\Requests\Tariffs\TariffUpdateRequest;
use App\Http\Resources\TariffResource;
use App\Models\Tariff;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TariffController extends Controller
{
    /**
     * Get paginated list of tariffs with filtering, sorting and pagination
     *
     * @param  TariffIndexRequest  $request
     * @param  GetTariffs  $action
     * @return AnonymousResourceCollection
     */
    public function index(TariffIndexRequest $request, GetTariffs $action): AnonymousResourceCollection
    {
        // Перевіряємо, чи користувач авторизований
        if (!auth()->check()) {
            abort(401, 'Unauthenticated');
        }

        $dto = TariffIndexDTO::fromRequest($request);
        $tariffs = $action->handle($dto);

        return TariffResource::collection($tariffs);
    }

    /**
     * Get detailed information about a specific tariff
     *
     * @param  Tariff  $tariff
     * @return TariffResource
     */
    public function show(Tariff $tariff): TariffResource
    {
        // Перевіряємо, чи користувач авторизований
        if (!auth()->check()) {
            abort(401, 'Unauthenticated');
        }

        return new TariffResource($tariff->loadCount('userSubscriptions'));
    }

    /**
     * Store a newly created tariff
     *
     * @param  TariffStoreRequest  $request
     * @param  CreateTariff  $action
     * @return TariffResource
     */
    public function store(TariffStoreRequest $request, CreateTariff $action): TariffResource
    {
        $dto = TariffStoreDTO::fromRequest($request);
        $tariff = $action->handle($dto);

        return new TariffResource($tariff);
    }

    /**
     * Update the specified tariff
     *
     * @param  TariffUpdateRequest  $request
     * @param  Tariff  $tariff
     * @param  UpdateTariff  $action
     * @return TariffResource
     */
    public function update(TariffUpdateRequest $request, Tariff $tariff, UpdateTariff $action): TariffResource
    {
        $dto = TariffUpdateDTO::fromRequest($request);
        $tariff = $action->handle($tariff, $dto);

        return new TariffResource($tariff);
    }

    /**
     * Remove the specified tariff
     *
     * @param  TariffDeleteRequest  $request
     * @param  Tariff  $tariff
     * @return JsonResponse
     */
    public function destroy(TariffDeleteRequest $request, Tariff $tariff): JsonResponse
    {
        // Check if the tariff has active subscriptions
        if ($tariff->userSubscriptions()->where('is_active', true)->exists()) {
            return response()->json([
                'message' => 'Cannot delete tariff with active subscriptions',
            ], 422);
        }

        $tariff->delete();

        return response()->json([
            'message' => 'Tariff deleted successfully',
        ]);
    }
}

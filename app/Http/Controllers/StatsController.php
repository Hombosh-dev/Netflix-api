<?php

namespace App\Http\Controllers;

use App\Actions\Stats\GetContentStats;
use App\Actions\Stats\GetPaymentStats;
use App\Actions\Stats\GetSubscriptionStats;
use App\Actions\Stats\GetUserStats;
use App\DTOs\Stats\StatsDTO;
use App\Http\Requests\Stats\StatsRequest;
use App\Http\Resources\Stats\ContentStatsResource;
use App\Http\Resources\Stats\PaymentStatsResource;
use App\Http\Resources\Stats\StatsResource;
use App\Http\Resources\Stats\SubscriptionStatsResource;
use App\Http\Resources\Stats\UserStatsResource;

class StatsController extends Controller
{
    /**
     * Get all statistics
     *
     * @param  StatsRequest  $request
     * @param  GetUserStats  $getUserStats
     * @param  GetContentStats  $getContentStats
     * @param  GetSubscriptionStats  $getSubscriptionStats
     * @param  GetPaymentStats  $getPaymentStats
     * @return StatsResource
     */
    public function index(
        StatsRequest $request,
        GetUserStats $getUserStats,
        GetContentStats $getContentStats,
        GetSubscriptionStats $getSubscriptionStats,
        GetPaymentStats $getPaymentStats
    ): StatsResource {
        $dto = StatsDTO::fromRequest($request);

        $stats = [
            'users' => $getUserStats->handle($dto),
            'content' => $getContentStats->handle($dto),
            'subscriptions' => $getSubscriptionStats->handle($dto),
            'payments' => $getPaymentStats->handle($dto),
        ];

        return new StatsResource($stats);
    }

    /**
     * Get user statistics
     *
     * @param  StatsRequest  $request
     * @param  GetUserStats  $action
     * @return UserStatsResource
     */
    public function users(StatsRequest $request, GetUserStats $action): UserStatsResource
    {
        $dto = StatsDTO::fromRequest($request);
        $stats = $action->handle($dto);

        return new UserStatsResource($stats);
    }

    /**
     * Get content statistics
     *
     * @param  StatsRequest  $request
     * @param  GetContentStats  $action
     * @return ContentStatsResource
     */
    public function content(StatsRequest $request, GetContentStats $action): ContentStatsResource
    {
        $dto = StatsDTO::fromRequest($request);
        $stats = $action->handle($dto);

        return new ContentStatsResource($stats);
    }

    /**
     * Get subscription statistics
     *
     * @param  StatsRequest  $request
     * @param  GetSubscriptionStats  $action
     * @return SubscriptionStatsResource
     */
    public function subscriptions(StatsRequest $request, GetSubscriptionStats $action): SubscriptionStatsResource
    {
        $dto = StatsDTO::fromRequest($request);
        $stats = $action->handle($dto);

        return new SubscriptionStatsResource($stats);
    }

    /**
     * Get payment statistics
     *
     * @param  StatsRequest  $request
     * @param  GetPaymentStats  $action
     * @return PaymentStatsResource
     */
    public function payments(StatsRequest $request, GetPaymentStats $action): PaymentStatsResource
    {
        $dto = StatsDTO::fromRequest($request);
        $stats = $action->handle($dto);

        return new PaymentStatsResource($stats);
    }
}

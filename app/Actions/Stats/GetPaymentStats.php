<?php

namespace App\Actions\Stats;

use App\DTOs\Stats\StatsDTO;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use Carbon\Carbon;
use Lorisleiva\Actions\Concerns\AsAction;

class GetPaymentStats
{
    use AsAction;

    /**
     * Get payment statistics.
     *
     * @param  StatsDTO  $dto
     * @return array
     */
    public function handle(StatsDTO $dto): array
    {
        $days = $dto->days;
        
        $totalPayments = Payment::count();
        $successfulPayments = Payment::where('status', PaymentStatus::SUCCESS)->count();
        $pendingPayments = Payment::where('status', PaymentStatus::PENDING)->count();
        $failedPayments = Payment::where('status', PaymentStatus::FAILED)->count();
        
        $totalAmount = Payment::where('status', PaymentStatus::SUCCESS)->sum('amount');
        $todayAmount = Payment::where('status', PaymentStatus::SUCCESS)
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');
        $thisWeekAmount = Payment::where('status', PaymentStatus::SUCCESS)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->sum('amount');
        $thisMonthAmount = Payment::where('status', PaymentStatus::SUCCESS)
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->sum('amount');
        $thisPeriodAmount = Payment::where('status', PaymentStatus::SUCCESS)
            ->where('created_at', '>=', Carbon::now()->subDays($days))
            ->sum('amount');

        return [
            'total_payments' => $totalPayments,
            'successful_payments' => $successfulPayments,
            'pending_payments' => $pendingPayments,
            'failed_payments' => $failedPayments,
            'total_amount' => $totalAmount,
            'today_amount' => $todayAmount,
            'this_week_amount' => $thisWeekAmount,
            'this_month_amount' => $thisMonthAmount,
            'this_period_amount' => $thisPeriodAmount,
        ];
    }
}

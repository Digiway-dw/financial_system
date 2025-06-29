<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Domain\Interfaces\TransactionRepository;
use App\Models\Domain\Entities\Line;
use Carbon\Carbon;

class ViewLineBalanceAndUsage
{
    public function __construct(
        private LineRepository $lineRepository,
        private TransactionRepository $transactionRepository
    ) {}

    public function execute(string $lineId): array
    {
        $line = $this->lineRepository->findById($lineId);

        if (!$line) {
            throw new \Exception('Line not found.');
        }

        // Calculate daily usage
        $startOfDay = Carbon::now()->startOfDay();
        $endOfDay = Carbon::now()->endOfDay();
        $dailyUsage = $this->transactionRepository->getTransactionsByLineAndDateRange(
            $lineId, $startOfDay, $endOfDay
        )->sum('amount');

        // Calculate monthly usage
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $monthlyUsage = $this->transactionRepository->getTransactionsByLineAndDateRange(
            $lineId, $startOfMonth, $endOfMonth
        )->sum('amount');

        return [
            'id' => $line->id,
            'mobile_number' => $line->mobile_number,
            'current_balance' => $line->current_balance,
            'daily_limit' => $line->daily_limit,
            'monthly_limit' => $line->monthly_limit,
            'network' => $line->network,
            'user_id' => $line->user_id,
            'status' => $line->status,
            'daily_usage' => $dailyUsage,
            'monthly_usage' => $monthlyUsage,
        ];
    }
} 
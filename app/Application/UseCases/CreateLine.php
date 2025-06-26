<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Models\Domain\Entities\Line;

class CreateLine
{
    private LineRepository $lineRepository;

    public function __construct(LineRepository $lineRepository)
    {
        $this->lineRepository = $lineRepository;
    }

    public function execute(
        string $mobileNumber,
        float $currentBalance,
        float $dailyLimit,
        float $monthlyLimit,
        string $network,
        string $userId
    ): Line
    {
        $attributes = [
            'mobile_number' => $mobileNumber,
            'current_balance' => $currentBalance,
            'daily_limit' => $dailyLimit,
            'monthly_limit' => $monthlyLimit,
            'network' => $network,
            'user_id' => $userId,
        ];

        return $this->lineRepository->create($attributes);
    }
} 
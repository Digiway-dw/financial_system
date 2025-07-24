<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Models\Domain\Entities\Line;

class CreateLine
{
    public function __construct(
        private LineRepository $lineRepository
    ) {}

    public function execute(array $lineData): Line
    {
        // Set starting_balance to current_balance if not provided
        if (!isset($lineData['starting_balance'])) {
            $lineData['starting_balance'] = $lineData['current_balance'] ?? 0;
        }
        // Set daily_starting_balance to current_balance when creating a new line
        $lineData['daily_starting_balance'] = $lineData['current_balance'] ?? 0;
        // Set daily_usage (daily received) to 0 when creating a new line
        $lineData['daily_usage'] = 0;
        // Initialize monthly usage and remaining correctly
        $lineData['monthly_usage'] = 0;
        $lineData['daily_remaining'] = ($lineData['daily_limit'] ?? 0) - ($lineData['current_balance'] ?? 0);
        $lineData['monthly_remaining'] = ($lineData['monthly_limit'] ?? 0) - ($lineData['current_balance'] ?? 0);
        return $this->lineRepository->create($lineData);
    }
} 
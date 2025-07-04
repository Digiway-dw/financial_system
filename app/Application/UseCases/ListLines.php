<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Application\UseCases\ViewLineBalanceAndUsage;

class ListLines
{
    private LineRepository $lineRepository;
    private ViewLineBalanceAndUsage $viewLineBalanceAndUsageUseCase;

    public function __construct(LineRepository $lineRepository, ViewLineBalanceAndUsage $viewLineBalanceAndUsageUseCase)
    {
        $this->lineRepository = $lineRepository;
        $this->viewLineBalanceAndUsageUseCase = $viewLineBalanceAndUsageUseCase;
    }

    public function execute(array $params = []): array
    {
        $sortField = $params['sortField'] ?? 'mobile_number';
        $sortDirection = $params['sortDirection'] ?? 'asc';
        $lines = $this->lineRepository->all($sortField, $sortDirection);
        return $lines;
    }
} 
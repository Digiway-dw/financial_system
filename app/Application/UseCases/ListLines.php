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

    public function execute(): array
    {
        $lines = $this->lineRepository->all();
        
        $detailedLines = [];
        foreach ($lines as $line) {
            $detailedLines[] = $this->viewLineBalanceAndUsageUseCase->execute($line->id);
        }

        return $detailedLines;
    }
} 
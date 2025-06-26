<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Models\Domain\Entities\Line;

class DeleteLine
{
    private LineRepository $lineRepository;

    public function __construct(LineRepository $lineRepository)
    {
        $this->lineRepository = $lineRepository;
    }

    public function execute(string $lineId): bool
    {
        $line = $this->lineRepository->findById($lineId);

        if (!$line) {
            return false;
        }

        $this->lineRepository->delete($lineId);

        return true;
    }
} 
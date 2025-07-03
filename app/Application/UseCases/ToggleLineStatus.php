<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Models\Domain\Entities\Line;

class ToggleLineStatus
{
    private LineRepository $lineRepository;

    public function __construct(LineRepository $lineRepository)
    {
        $this->lineRepository = $lineRepository;
    }

    public function execute(string $lineId): Line
    {
        $line = $this->lineRepository->findById($lineId);
        if (!$line) {
            throw new \Exception('Line not found.');
        }

        // Toggle the status
        $newStatus = ($line->status === 'active') ? 'inactive' : 'active';

        $this->lineRepository->update($lineId, ['status' => $newStatus]);

        return $line->fresh();
    }
} 
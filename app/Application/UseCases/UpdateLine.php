<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Models\Domain\Entities\Line;

class UpdateLine
{
    public function __construct(
        private LineRepository $lineRepository
    ) {}

    public function execute(string $lineId, array $lineData): Line
    {
        $line = $this->lineRepository->findById($lineId);

        if (!$line) {
            throw new \Exception('Line not found.');
        }

        // Add any business rules or validations here before updating the line

        return $this->lineRepository->update($lineId, $lineData);
    }
} 
<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;

class DeleteLine
{
    public function __construct(
        private LineRepository $lineRepository
    ) {}

    public function execute(string $lineId): void
    {
        $line = $this->lineRepository->findById($lineId);

        if (!$line) {
            throw new \Exception('Line not found.');
        }

        // Add any business rules or validations here before deleting the line
        // For example, preventing deletion if the line has associated active transactions

        $this->lineRepository->delete($lineId);
    }
} 
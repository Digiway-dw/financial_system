<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\SafeRepository;

class DeleteSafe
{
    public function __construct(
        private SafeRepository $safeRepository
    ) {}

    public function execute(string $safeId): void
    {
        $safe = $this->safeRepository->findById($safeId);

        if (!$safe) {
            throw new \Exception('Safe not found.');
        }

        // Add any business rules or validations here before deleting the safe
        // For example, preventing deletion if the safe has a non-zero balance

        $this->safeRepository->delete($safeId);
    }
} 
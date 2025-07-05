<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\SafeRepository;
use App\Models\Domain\Entities\Safe;

class UpdateSafe
{
    public function __construct(
        private SafeRepository $safeRepository
    ) {}

    public function execute(string $safeId, array $safeData): Safe
    {
        $safe = $this->safeRepository->findById($safeId);

        if (!$safe) {
            throw new \Exception('Safe not found.');
        }

        // Remove branch_id from safeData to prevent changing the branch
        unset($safeData['branch_id']);

        // Add any business rules or validations here before updating the safe

        return $this->safeRepository->update($safeId, $safeData);
    }
} 
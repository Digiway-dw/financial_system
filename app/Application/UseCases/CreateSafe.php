<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\SafeRepository;
use App\Models\Domain\Entities\Safe;

class CreateSafe
{
    public function __construct(
        private SafeRepository $safeRepository
    ) {}

    public function execute(array $safeData): Safe
    {
        // Add any business rules or validations here before creating the safe
        return $this->safeRepository->create($safeData);
    }
} 
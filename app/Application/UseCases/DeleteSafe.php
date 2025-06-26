<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\SafeRepository;
use App\Models\Domain\Entities\Safe;

class DeleteSafe
{
    private SafeRepository $safeRepository;

    public function __construct(SafeRepository $safeRepository)
    {
        $this->safeRepository = $safeRepository;
    }

    public function execute(string $safeId): bool
    {
        $safe = $this->safeRepository->findById($safeId);

        if (!$safe) {
            return false;
        }

        $this->safeRepository->delete($safeId);

        return true;
    }
} 
<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\SafeRepository;
use App\Models\Domain\Entities\Safe;

class UpdateSafe
{
    private SafeRepository $safeRepository;

    public function __construct(SafeRepository $safeRepository)
    {
        $this->safeRepository = $safeRepository;
    }

    public function execute(
        string $safeId,
        array $attributes
    ): Safe
    {
        $safe = $this->safeRepository->findById($safeId);

        if (!$safe) {
            throw new \Exception('Safe not found.');
        }

        return $this->safeRepository->update($safeId, $attributes);
    }
} 
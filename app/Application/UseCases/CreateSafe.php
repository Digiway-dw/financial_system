<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\SafeRepository;
use App\Models\Domain\Entities\Safe;

class CreateSafe
{
    private SafeRepository $safeRepository;

    public function __construct(SafeRepository $safeRepository)
    {
        $this->safeRepository = $safeRepository;
    }

    public function execute(
        string $name,
        float $balance,
        string $branchId,
        ?string $description
    ): Safe
    {
        $attributes = [
            'name' => $name,
            'balance' => $balance,
            'branch_id' => $branchId,
            'description' => $description,
        ];

        return $this->safeRepository->create($attributes);
    }
} 
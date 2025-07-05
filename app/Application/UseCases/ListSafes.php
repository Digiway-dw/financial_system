<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\SafeRepository;

class ListSafes
{
    public function __construct(
        private SafeRepository $safeRepository
    ) {}

    public function execute($name = null): array
    {
        return $this->safeRepository->allWithBranch($name);
    }
} 
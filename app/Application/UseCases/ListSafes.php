<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\SafeRepository;

class ListSafes
{
    public function __construct(
        private SafeRepository $safeRepository
    ) {}

    public function execute(): array
    {
        return $this->safeRepository->all();
    }
} 
<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\SafeRepository;

class ListSafes
{
    private SafeRepository $safeRepository;

    public function __construct(SafeRepository $safeRepository)
    {
        $this->safeRepository = $safeRepository;
    }

    public function execute(): array
    {
        return $this->safeRepository->all();
    }
} 
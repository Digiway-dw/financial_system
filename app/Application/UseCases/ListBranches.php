<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\BranchRepository;

class ListBranches
{
    public function __construct(
        private BranchRepository $branchRepository
    ) {}

    public function execute(): array
    {
        return $this->branchRepository->all();
    }
} 
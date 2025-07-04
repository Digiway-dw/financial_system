<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\BranchRepository;
use Illuminate\Database\Eloquent\Collection;

class ListBranches
{
    public function __construct(
        private BranchRepository $branchRepository
    ) {}

    public function execute(): Collection
    {
        return $this->branchRepository->all();
    }
} 
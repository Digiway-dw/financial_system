<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\BranchRepository;
use App\Models\Domain\Entities\Branch;

class CreateBranch
{
    public function __construct(
        private BranchRepository $branchRepository
    ) {}

    public function execute(array $branchData): Branch
    {
        // Add any business rules or validations here before creating the branch

        return $this->branchRepository->create($branchData);
    }
} 
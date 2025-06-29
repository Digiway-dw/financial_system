<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\BranchRepository;
use App\Models\Domain\Entities\Branch;

class UpdateBranch
{
    public function __construct(
        private BranchRepository $branchRepository
    ) {}

    public function execute(string $branchId, array $branchData): Branch
    {
        $branch = $this->branchRepository->findById($branchId);

        if (!$branch) {
            throw new \Exception('Branch not found.');
        }

        // Add any business rules or validations here before updating the branch

        return $this->branchRepository->update($branchId, $branchData);
    }
} 
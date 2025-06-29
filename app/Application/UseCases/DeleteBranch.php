<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\BranchRepository;

class DeleteBranch
{
    public function __construct(
        private BranchRepository $branchRepository
    ) {}

    public function execute(string $branchId): void
    {
        $branch = $this->branchRepository->findById($branchId);

        if (!$branch) {
            throw new \Exception('Branch not found.');
        }

        // Add any business rules or validations here before deleting the branch
        // For example, preventing deletion if the branch has associated users, safes, or transactions

        $this->branchRepository->delete($branchId);
    }
} 
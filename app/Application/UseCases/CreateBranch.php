<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\BranchRepository;
use App\Domain\Interfaces\SafeRepository;
use App\Models\Domain\Entities\Branch;

class CreateBranch
{
    public function __construct(
        private BranchRepository $branchRepository,
        private SafeRepository $safeRepository
    ) {}

    public function execute(array $branchData): Branch
    {
        // Extract safe-related data from branch data
        $safeInitialBalance = $branchData['safe_initial_balance'] ?? 0.00;
        $safeDescription = $branchData['safe_description'] ?? '';

        // Remove safe-related keys from branch data before creating branch
        $branchOnlyData = array_diff_key($branchData, array_flip(['safe_initial_balance', 'safe_description']));

        // Create the branch first
        $branch = $this->branchRepository->create($branchOnlyData);

        // Create a safe for this branch using the branch name + ' Safe' and type 'branch'
        $this->safeRepository->create([
            'name' => $branch->name . ' Safe',
            'current_balance' => $safeInitialBalance,
            'branch_id' => $branch->id,
            'description' => $safeDescription,
            'type' => 'branch',
        ]);

        return $branch;
    }
}

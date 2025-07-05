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
        // Create the branch first
        $branch = $this->branchRepository->create($branchData);

        // Create a safe for this branch using the branch name + ' Safe' and type 'branch'
        $this->safeRepository->create([
            'name' => $branch->name . ' Safe',
            'current_balance' => $branchData['safe_initial_balance'],
            'branch_id' => $branch->id,
            'description' => $branchData['safe_description'],
            'type' => 'branch',
        ]);

        return $branch;
    }
} 
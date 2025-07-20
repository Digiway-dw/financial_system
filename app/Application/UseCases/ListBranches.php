<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\BranchRepository;
use Illuminate\Database\Eloquent\Collection;

class ListBranches
{
    public function __construct(
        private BranchRepository $branchRepository
    ) {}

    public function execute($filters = null): Collection
    {
        // Handle both null (legacy) and array (new) parameters
        if (is_null($filters)) {
            return $this->branchRepository->all();
        }
        
        // Extract sorting parameters from filters array
        $sortField = $filters['sortField'] ?? 'name';
        $sortDirection = $filters['sortDirection'] ?? 'asc';
        
        return $this->branchRepository->all($sortField, $sortDirection);
    }
} 
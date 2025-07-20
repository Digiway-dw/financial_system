<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\SafeRepository;

class ListSafes
{
    public function __construct(
        private SafeRepository $safeRepository
    ) {}

    public function execute($filters = null): array
    {
        // Handle both string (legacy) and array (new) parameters
        if (is_string($filters)) {
            return $this->safeRepository->allWithBranch($filters);
        }
        
        // Extract parameters from filters array
        $name = $filters['name'] ?? null;
        $sortField = $filters['sortField'] ?? 'name';
        $sortDirection = $filters['sortDirection'] ?? 'asc';
        
        return $this->safeRepository->allWithBranch($name, $sortField, $sortDirection);
    }
} 
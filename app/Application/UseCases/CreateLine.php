<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Models\Domain\Entities\Line;

class CreateLine
{
    public function __construct(
        private LineRepository $lineRepository
    ) {}

    public function execute(array $lineData): Line
    {
        // Add any business rules or validations here before creating the line
        // For example, ensuring unique mobile number, valid network, etc.

        return $this->lineRepository->create($lineData);
    }
} 
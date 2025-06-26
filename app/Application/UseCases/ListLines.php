<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;

class ListLines
{
    private LineRepository $lineRepository;

    public function __construct(LineRepository $lineRepository)
    {
        $this->lineRepository = $lineRepository;
    }

    public function execute(): array
    {
        return $this->lineRepository->all();
    }
} 
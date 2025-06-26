<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Models\Domain\Entities\Line;

class UpdateLine
{
    private LineRepository $lineRepository;

    public function __construct(LineRepository $lineRepository)
    {
        $this->lineRepository = $lineRepository;
    }

    public function execute(
        string $lineId,
        array $attributes
    ): Line
    {
        $line = $this->lineRepository->findById($lineId);

        if (!$line) {
            throw new \Exception('Line not found.');
        }

        return $this->lineRepository->update($lineId, $attributes);
    }
} 
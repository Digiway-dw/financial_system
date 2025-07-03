<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Models\Domain\Entities\Line;

class UpdateLineNetwork
{
    private LineRepository $lineRepository;

    public function __construct(LineRepository $lineRepository)
    {
        $this->lineRepository = $lineRepository;
    }

    public function execute(string $lineId, string $newNetwork): Line
    {
        $line = $this->lineRepository->findById($lineId);
        if (!$line) {
            throw new \Exception('Line not found.');
        }

        $this->lineRepository->update($lineId, ['network' => $newNetwork]);

        return $line->fresh();
    }
} 
<?php

namespace App\Application\UseCases;

use App\Domain\Interfaces\LineRepository;
use App\Domain\Interfaces\UserRepository;
use App\Models\Domain\Entities\Line;

class TransferLine
{
    private LineRepository $lineRepository;
    private UserRepository $userRepository;

    public function __construct(LineRepository $lineRepository, UserRepository $userRepository)
    {
        $this->lineRepository = $lineRepository;
        $this->userRepository = $userRepository;
    }

    public function execute(string $lineId, string $newUserId): Line
    {
        $line = $this->lineRepository->findById($lineId);
        if (!$line) {
            throw new \Exception('Line not found.');
        }

        $newUser = $this->userRepository->findById($newUserId);
        if (!$newUser) {
            throw new \Exception('New user not found.');
        }

        // Update the user_id of the line
        $line->user_id = $newUserId;
        $this->lineRepository->update($line->id, ['user_id' => $newUserId]);

        return $line;
    }
} 
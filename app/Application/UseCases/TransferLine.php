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
        throw new \Exception('Transferring lines by user is not supported: lines table has no user_id column.');
    }
} 
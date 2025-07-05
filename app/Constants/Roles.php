<?php

namespace App\Constants;

class Roles
{
    public const ADMIN = 'admin';
    public const GENERAL_SUPERVISOR = 'general_supervisor';
    public const BRANCH_MANAGER = 'branch_manager';
    public const AGENT = 'agent';
    public const AUDITOR = 'auditor';
    public const TRAINEE = 'trainee';

    public static function all(): array
    {
        return [
            self::ADMIN,
            self::GENERAL_SUPERVISOR,
            self::BRANCH_MANAGER,
            self::AGENT,
            self::AUDITOR,
            self::TRAINEE,
        ];
    }

    public static function managerialRoles(): array
    {
        return [
            self::ADMIN,
            self::GENERAL_SUPERVISOR,
            self::BRANCH_MANAGER,
        ];
    }

    public static function supervisoryRoles(): array
    {
        return [
            self::ADMIN,
            self::GENERAL_SUPERVISOR,
        ];
    }
}

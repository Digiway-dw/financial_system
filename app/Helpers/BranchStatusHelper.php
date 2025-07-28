<?php

namespace App\Helpers;

use App\Models\Domain\Entities\Branch;
use Illuminate\Support\Facades\Auth;

class BranchStatusHelper
{
    /**
     * Check if a branch is active
     */
    public static function isBranchActive($branchId): bool
    {
        $branch = Branch::find($branchId);
        return $branch && $branch->is_active;
    }

    /**
     * Check if the current user's branch is active
     */
    public static function isCurrentUserBranchActive(): bool
    {
        $user = Auth::user();
        if (!$user || !$user->branch_id) {
            return false;
        }
        return self::isBranchActive($user->branch_id);
    }

    /**
     * Validate branch status and throw exception if inactive
     */
    public static function validateBranchActive($branchId): void
    {
        if (!self::isBranchActive($branchId)) {
            throw new \Exception('لا يمكن إجراء المعاملات في هذا الفرع لأنه غير نشط. يرجى الاتصال بالإدارة.');
        }
    }

    /**
     * Validate current user's branch status and throw exception if inactive
     */
    public static function validateCurrentUserBranchActive(): void
    {
        if (!self::isCurrentUserBranchActive()) {
            throw new \Exception('لا يمكن إجراء المعاملات في هذا الفرع لأنه غير نشط. يرجى الاتصال بالإدارة.');
        }
    }

    /**
     * Get branch status message
     */
    public static function getBranchStatusMessage($branchId): string
    {
        $branch = Branch::find($branchId);
        if (!$branch) {
            return 'الفرع غير موجود.';
        }
        return $branch->is_active ? 'الفرع نشط' : 'الفرع غير نشط';
    }
} 
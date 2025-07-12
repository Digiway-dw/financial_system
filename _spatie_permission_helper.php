<?php

/**
 * Custom IDE Helper for Financial System
 * 
 * This file provides IDE support for commonly used methods and traits
 * without the conflicts and warnings from auto-generated helpers.
 * 
 * @see https://spatie.be/docs/laravel-permission
 */

// Prevent this file from being executed
if (false) {

    /**
     * IDE Helper for User model with Spatie Permission methods
     * 
     * @mixin \Spatie\Permission\Traits\HasRoles
     */
    class User extends \App\Domain\Entities\User
    {
        // Spatie Permission Role Methods
        public function hasRole($roles, string $guard = null): bool
        {
            return true;
        }
        public function hasAnyRole($roles, string $guard = null): bool
        {
            return true;
        }
        public function hasAllRoles($roles, string $guard = null): bool
        {
            return true;
        }
        public function assignRole($roles): self
        {
            return $this;
        }
        public function removeRole($roles): self
        {
            return $this;
        }
        public function syncRoles($roles): self
        {
            return $this;
        }
        public function getRoleNames(): \Illuminate\Support\Collection
        {
            return collect();
        }

        // Spatie Permission Methods
        public function hasPermissionTo($permission, string $guard = null): bool
        {
            return true;
        }
        public function hasAnyPermission($permissions, string $guard = null): bool
        {
            return true;
        }
        public function hasAllPermissions($permissions, string $guard = null): bool
        {
            return true;
        }
        public function hasDirectPermission($permission, string $guard = null): bool
        {
            return true;
        }
        public function givePermissionTo($permissions): self
        {
            return $this;
        }
        public function revokePermissionTo($permissions): self
        {
            return $this;
        }
        public function syncPermissions($permissions): self
        {
            return $this;
        }
        public function getAllPermissions(): \Illuminate\Support\Collection
        {
            return collect();
        }
        public function getDirectPermissions(): \Illuminate\Support\Collection
        {
            return collect();
        }
        public function getPermissionsViaRoles(): \Illuminate\Support\Collection
        {
            return collect();
        }
        public function getPermissionNames(): \Illuminate\Support\Collection
        {
            return collect();
        }

        // Laravel Authorization
        public function can($ability, $arguments = []): bool
        {
            return true;
        }
        public function cannot($ability, $arguments = []): bool
        {
            return true;
        }
    }

    /**
     * IDE Helper for commonly used Laravel classes
     */

    // Auth Facade
    class Auth extends \Illuminate\Support\Facades\Auth
    {
        public static function user(): ?\App\Domain\Entities\User
        {
            return new \App\Domain\Entities\User();
        }
        public static function id(): ?int
        {
            return 1;
        }
        public static function check(): bool
        {
            return true;
        }
        public static function guest(): bool
        {
            return false;
        }
    }

    // Session Helper
    class Session
    {
        public static function flash(string $key, $value = null): void {}
        public static function get(string $key, $default = null)
        {
            return $default;
        }
        public static function put(string $key, $value): void {}
        public static function forget(string $key): void {}
    }

    // Collection Helper (for better autocomplete)
    class Collection extends \Illuminate\Support\Collection
    {
        public function map(callable $callback): self
        {
            return $this;
        }
        public function filter(callable $callback = null): self
        {
            return $this;
        }
        public function first(callable $callback = null, $default = null)
        {
            return $default;
        }
        public function pluck($value, $key = null): self
        {
            return $this;
        }
        public function toArray(): array
        {
            return [];
        }
        public function count(): int
        {
            return 0;
        }
        public function isEmpty(): bool
        {
            return true;
        }
        public function isNotEmpty(): bool
        {
            return false;
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Role & permission dasar (Spatie).
 *
 * Kolom `users.role` tetap jadi sumber utama otorisasi panel
 * (lihat User::canAccessPanel); seeder ini menyinkronkan role
 * Spatie agar permission granular bisa dipakai bertahap.
 * Idempotent: aman dijalankan ulang.
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * @var array<string, list<string>>
     */
    public const ROLE_PERMISSIONS = [
        'admin' => [
            'manage users',
            'manage roles',
            'manage properties',
            'manage leads',
            'manage orders',
            'manage payments',
            'manage content',
        ],
        'agent' => [
            'manage properties',
        ],
        'owner' => [],
        'user' => [],
    ];

    public function run(): void
    {
        $permissions = [];
        foreach (self::ROLE_PERMISSIONS as $abilities) {
            foreach ($abilities as $ability) {
                $permissions[$ability] = true;
            }
        }

        foreach (array_keys($permissions) as $ability) {
            Permission::query()->firstOrCreate(['name' => $ability, 'guard_name' => 'web']);
        }

        foreach (self::ROLE_PERMISSIONS as $roleName => $abilities) {
            $role = Role::query()->firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($abilities);
        }
    }
}

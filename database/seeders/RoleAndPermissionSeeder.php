<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use RuntimeException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    private const GUARD = 'web';

    private const STRATEGIC_PERMISSIONS = [
        'view_strategic_metrics',
        'view_audit_logs',
        'manage_system_settings',
    ];

    private const FLEET_PERMISSIONS = [
        'view_fleet',
        'manage_driver_shifts',
        'collect_driver_cash',
        'manage_driver_assets',
    ];

    private const SUPPORT_PERMISSIONS = [
        'view_support_tickets',
        'manage_support_tickets',
        'issue_customer_compensation',
    ];

    private const ACCOUNTING_PERMISSIONS = [
        'view_daily_cashflow',
        'reconcile_driver_cash',
        'record_operational_expenses',
        'view_operational_ledger',
    ];

    /**
     * Roles behind the "Corporate Veil": they must never hold a strategic
     * permission (equity, investor metrics, CAC, LTV, Runway, audit logs).
     */
    private const VEILED_ROLES = [
        'call_center_supervisor',
        'boss_delivery',
        'accountant',
    ];

    /**
     * Run the database seeds.
     *
     * Safe to run repeatedly: permissions and roles are looked up before they
     * are created, and each role's permissions are synced to the exact set
     * declared below, so any drift is corrected on every run.
     */
    public function run(): void
    {
        $registrar = app(PermissionRegistrar::class);
        $registrar->forgetCachedPermissions();

        $matrix = $this->rolePermissionMatrix();
        $this->assertCorporateVeil($matrix);

        foreach ($this->allPermissions() as $permission) {
            Permission::findOrCreate($permission, self::GUARD);
        }

        foreach ($matrix as $roleName => $permissions) {
            Role::findOrCreate($roleName, self::GUARD)->syncPermissions($permissions);
        }

        $registrar->forgetCachedPermissions();
    }

    /**
     * Every permission in the system.
     *
     * @return list<string>
     */
    private function allPermissions(): array
    {
        return [
            ...self::STRATEGIC_PERMISSIONS,
            ...self::FLEET_PERMISSIONS,
            ...self::SUPPORT_PERMISSIONS,
            ...self::ACCOUNTING_PERMISSIONS,
        ];
    }

    /**
     * The exact permission set each role is allowed to hold.
     *
     * @return array<string, list<string>>
     */
    private function rolePermissionMatrix(): array
    {
        return [
            // Co-Founder / Super Admin: unrestricted.
            'ctmo' => $this->allPermissions(),

            // Co-Founder / Operations Executive: shared executive dashboard,
            // audit visibility and operational overrides in every domain, but
            // no technical system settings.
            'coo' => [
                'view_strategic_metrics',
                'view_audit_logs',
                ...self::FLEET_PERMISSIONS,
                ...self::SUPPORT_PERMISSIONS,
                ...self::ACCOUNTING_PERMISSIONS,
            ],

            // Support only, including the emergency "Rescue Button".
            'call_center_supervisor' => self::SUPPORT_PERMISSIONS,

            // Fleet, shifts, asset custody and daily driver cash collection.
            'boss_delivery' => self::FLEET_PERMISSIONS,

            // Reconciliation, petty cash and OpEx only.
            'accountant' => self::ACCOUNTING_PERMISSIONS,
        ];
    }

    /**
     * Refuse to seed if the matrix ever grants a strategic permission to a
     * veiled role, so a careless edit cannot silently breach the veil.
     *
     * @param  array<string, list<string>>  $matrix
     */
    private function assertCorporateVeil(array $matrix): void
    {
        foreach (self::VEILED_ROLES as $roleName) {
            $leaked = array_intersect($matrix[$roleName] ?? [], self::STRATEGIC_PERMISSIONS);

            if ($leaked !== []) {
                throw new RuntimeException(sprintf(
                    'Corporate Veil breach: role "%s" must not hold [%s].',
                    $roleName,
                    implode(', ', $leaked)
                ));
            }
        }
    }
}

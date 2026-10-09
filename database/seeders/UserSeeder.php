<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    private const TEMPORARY_PASSWORD = 'TemporaryPassword123!';

    /**
     * Run the database seeds.
     *
     * Requires RoleAndPermissionSeeder to have run first. The whole run is one
     * transaction, so a missing role never leaves half-seeded accounts behind.
     *
     * Re-running is safe and non-destructive: the password is only set when an
     * account is first created, so it never overwrites a password the user has
     * since changed, and `status` is never touched, so a suspended account is
     * never silently re-activated. The role is re-synced to the declared one.
     */
    public function run(): void
    {
        DB::transaction(function (): void {
            foreach ($this->accounts() as $account) {
                $attributes = ['name' => $account['name']];

                if (! User::where('email', $account['email'])->exists()) {
                    $attributes['password'] = Hash::make(self::TEMPORARY_PASSWORD);
                }

                $user = User::updateOrCreate(['email' => $account['email']], $attributes);

                $user->syncRoles([$account['role']]);
            }
        });
    }

    /**
     * @return list<array{name: string, email: string, role: string}>
     */
    private function accounts(): array
    {
        return [
            [
                'name' => 'Mohamed',
                'email' => 'mohamed@martendo.internal',
                'role' => 'ctmo',
            ],
            [
                'name' => 'Ahmed',
                'email' => 'ahmed@martendo.internal',
                'role' => 'coo',
            ],
            [
                'name' => 'Doha',
                'email' => 'doha@martendo.internal',
                'role' => 'call_center_supervisor',
            ],
            [
                'name' => 'Ammar Najjar',
                'email' => 'ammar.najjar@martendo.internal',
                'role' => 'boss_delivery',
            ],
            [
                'name' => 'Accountant Ammar',
                'email' => 'accountant@martendo.internal',
                'role' => 'accountant',
            ],
        ];
    }
}

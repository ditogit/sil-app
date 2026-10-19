<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\WorkUnit;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = WorkUnit::pluck('id', 'code');

        $core = [
            [
                'email' => 'admin@example.com',
                'name' => 'Achmad Fauzi, S.Kom., M.Eng.',
                'nip' => '19850412 201012 1 002',
                'position' => 'Superadmin',
                'unit' => 'BSI',
                'campus' => 'Babarsari Campus',
                'role' => 'admin',
                'two_factor' => true,
                'last_login' => now()->subMinutes(5),
            ],
            [
                'email' => 'pm@example.com',
                'name' => 'Siti Nurhaliza, S.Kom.',
                'nip' => '19920815 201903 2 007',
                'position' => 'Lead Backend',
                'unit' => 'BSI-BE',
                'campus' => 'Babarsari Campus',
                'role' => 'project_manager',
                'two_factor' => true,
                'last_login' => now()->subMinutes(12),
            ],
            [
                'email' => 'member@example.com',
                'name' => 'Rizky Pratama, S.Kom.',
                'nip' => '19950622 202204 1 009',
                'position' => 'Frontend Engineer',
                'unit' => 'BSI-FE',
                'campus' => 'Condongcatur Campus',
                'role' => 'team_member',
                'two_factor' => true,
                'last_login' => now()->subHours(2),
            ],
            [
                'email' => 'viewer@example.com',
                'name' => 'Ir. H. Bambang Sudarsono, M.T.',
                'nip' => '19680517 199403 1 001',
                'position' => 'Faculty PIC / Viewer',
                'unit' => 'FTI',
                'campus' => 'Babarsari Campus',
                'role' => 'viewer',
                'two_factor' => true,
                'last_login' => now()->subDays(3),
            ],
        ];

        $extra = [
            ['Dimas Arya Pradana, S.T.', 'dev.ops@example.com', 'DevOps & Infra', 'BSI-OPS', 'Babarsari Data Center', 'project_manager', true, 60],
            ['Budi Santoso, M.Kom.', 'qa@example.com', 'QA & Testing Lead', 'BSI-QA', 'Babarsari Campus', 'team_member', true, 1560],
            ['Maya Anggraini, S.Ds.', 'ux@example.com', 'UI/UX Designer', 'BSI-UX', 'Information Systems Bureau', 'team_member', true, 1560],
            ['Nadia Puspita, S.Kom.', 'nadia@example.com', 'Backend Engineer', 'BSI-BE', 'Babarsari Campus', 'team_member', false, 2880],
            ['Fajar Nugroho, S.T.', 'fajar@example.com', 'SRE', 'BSI-OPS', 'Babarsari Data Center', 'team_member', true, 720],
            ['Intan Permata, S.Kom.', 'intan@example.com', 'QA Engineer', 'BSI-QA', 'Babarsari Campus', 'team_member', false, 10080],
        ];

        foreach ($core as $row) {
            $user = User::updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'password' => Hash::make('password'),
                    'nip' => $row['nip'],
                    'position' => $row['position'],
                    'work_unit_id' => $units[$row['unit']] ?? null,
                    'campus' => $row['campus'],
                    'status' => 'active',
                    'two_factor_confirmed_at' => $row['two_factor'] ? now() : null,
                    'last_login_at' => $row['last_login'],
                ]
            );

            $user->syncRoles([$row['role']]);
        }

        foreach ($extra as [$name, $email, $position, $unit, $campus, $role, $twoFactor, $minutesAgo]) {
            $user = User::updateOrCreate(
                ['email' => $email],
                [
                    'name' => $name,
                    'password' => Hash::make('password'),
                    'position' => $position,
                    'work_unit_id' => $units[$unit] ?? null,
                    'campus' => $campus,
                    'status' => $twoFactor ? 'active' : 'pending_2fa',
                    'two_factor_confirmed_at' => $twoFactor ? now() : null,
                    'last_login_at' => now()->subMinutes($minutesAgo),
                ]
            );

            $user->syncRoles([$role]);
        }
    }
}

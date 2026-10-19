<?php

namespace Database\Seeders;

use App\Models\WorkUnit;
use Illuminate\Database\Seeder;

class WorkUnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Information Systems Bureau', 'code' => 'BSI', 'campus' => 'Babarsari Campus', 'type' => 'bureau'],
            ['name' => 'BSI Core Backend', 'code' => 'BSI-BE', 'campus' => 'Babarsari Campus', 'type' => 'bureau'],
            ['name' => 'Infrastructure & DevOps', 'code' => 'BSI-OPS', 'campus' => 'Babarsari Data Center', 'type' => 'bureau'],
            ['name' => 'Frontend & Interface', 'code' => 'BSI-FE', 'campus' => 'Condongcatur Campus', 'type' => 'bureau'],
            ['name' => 'QA & Software Quality', 'code' => 'BSI-QA', 'campus' => 'Babarsari Campus', 'type' => 'bureau'],
            ['name' => 'Product Design & UI/UX', 'code' => 'BSI-UX', 'campus' => 'Information Systems Bureau', 'type' => 'bureau'],
            ['name' => "Dean's Office, Fac. of Industrial Engineering", 'code' => 'FTI', 'campus' => 'Babarsari Campus', 'type' => 'faculty'],
            ['name' => 'Rectorate & LPPM', 'code' => 'RECTOR', 'campus' => 'Babarsari Campus', 'type' => 'rectorate'],
        ];

        foreach ($units as $unit) {
            WorkUnit::firstOrCreate(['code' => $unit['code']], $unit);
        }
    }
}

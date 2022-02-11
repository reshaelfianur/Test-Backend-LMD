<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SubModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('sub_modules')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        \DB::table('sub_modules')->insert([
            [
                'submod_id'     => 1,
                'mod_id'        => 1,
                'submod_code'   => 'company',
                'submod_name'   => 'Company',
            ],
            [
                'submod_id'     => 2,
                'mod_id'        => 1,
                'submod_code'   => 'bank',
                'submod_name'   => 'Bank',
            ],
            [
                'submod_id'     => 3,
                'mod_id'        => 1,
                'submod_code'   => 'bpjs-worker',
                'submod_name'   => 'Bpjs Worker',
            ],
            [
                'submod_id'     => 4,
                'mod_id'        => 1,
                'submod_code'   => 'bpjs-healthcare',
                'submod_name'   => 'Bpjs Healthcare',
            ],
            [
                'submod_id'     => 5,
                'mod_id'        => 1,
                'submod_code'   => 'tax',
                'submod_name'   => 'Tax',
            ],
            [
                'submod_id'     => 6,
                'mod_id'        => 1,
                'submod_code'   => 'attr-dept1',
                'submod_name'   => 'Attributes Department #1',
            ],
            [
                'submod_id'     => 7,
                'mod_id'        => 1,
                'submod_code'   => 'attr-dept2',
                'submod_name'   => 'Attributes Department #2',
            ],
            [
                'submod_id'     => 8,
                'mod_id'        => 1,
                'submod_code'   => 'attr-dept3',
                'submod_name'   => 'Attributes Department #3',
            ],
            [
                'submod_id'     => 9,
                'mod_id'        => 1,
                'submod_code'   => 'attr-dept4',
                'submod_name'   => 'Attributes Department #4',
            ],
            [
                'submod_id'     => 10,
                'mod_id'        => 1,
                'submod_code'   => 'grade',
                'submod_name'   => 'Grade',
            ],
            [
                'submod_id'     => 11,
                'mod_id'        => 1,
                'submod_code'   => 'job-title',
                'submod_name'   => 'Job Title',
            ],
            [
                'submod_id'     => 12,
                'mod_id'        => 1,
                'submod_code'   => 'location',
                'submod_name'   => 'Location',
            ],
            [
                'submod_id'     => 13,
                'mod_id'        => 1,
                'submod_code'   => 'maximum-os',
                'submod_name'   => 'Maximum OS',
            ],
            [
                'submod_id'     => 14,
                'mod_id'        => 1,
                'submod_code'   => 'nontax-income',
                'submod_name'   => 'Non-taxable Income',
            ],
            [
                'submod_id'     => 15,
                'mod_id'        => 1,
                'submod_code'   => 'Severance-tariff',
                'submod_name'   => 'Severance Tariff',
            ],
            [
                'submod_id'     => 16,
                'mod_id'        => 1,
                'submod_code'   => 'Tax-by-government',
                'submod_name'   => 'Tax by Government',
            ],
            [
                'submod_id'     => 17,
                'mod_id'        => 1,
                'submod_code'   => 'Tax-tariff',
                'submod_name'   => 'Tax Tariff',
            ],
            [
                'submod_id'     => 18,
                'mod_id'        => 1,
                'submod_code'   => 'team',
                'submod_name'   => 'Team',
            ],
            [
                'submod_id'     => 19,
                'mod_id'        => 2,
                'submod_code'   => 'payroll-account',
                'submod_name'   => 'Payroll Account',
            ],
            [
                'submod_id'     => 20,
                'mod_id'        => 3,
                'submod_code'   => 'employee',
                'submod_name'   => 'Employee',
            ],
            [
                'submod_id'     => 21,
                'mod_id'        => 3,
                'submod_code'   => 'Import-employee',
                'submod_name'   => 'Import Employee',
            ],
            [
                'submod_id'     => 22,
                'mod_id'        => 4,
                'submod_code'   => 'Variable-income',
                'submod_name'   => 'Variable Income',
            ],
            [
                'submod_id'     => 23,
                'mod_id'        => 4,
                'submod_code'   => 'Import-income',
                'submod_name'   => 'Import Income',
            ],
            [
                'submod_id'     => 24,
                'mod_id'        => 4,
                'submod_code'   => 'Payroll-processing',
                'submod_name'   => 'Payroll Processing',
            ],
            [
                'submod_id'     => 25,
                'mod_id'        => 4,
                'submod_code'   => 'Payroll-closing',
                'submod_name'   => 'Payroll Closing',
            ],
            [
                'submod_id'     => 26,
                'mod_id'        => 4,
                'submod_code'   => 'Setback-period',
                'submod_name'   => 'Set Back Period',
            ],
            [
                'submod_id'     => 27,
                'mod_id'        => 5,
                'submod_code'   => 'payroll-detail',
                'submod_name'   => 'Payroll Detail Report',
            ],
            [
                'submod_id'     => 28,
                'mod_id'        => 5,
                'submod_code'   => 'bpjs-worker',
                'submod_name'   => 'BPJS Worker Report',
            ],
            [
                'submod_id'     => 29,
                'mod_id'        => 5,
                'submod_code'   => 'bpjs-healthcare',
                'submod_name'   => 'BPJS Healthcare Report',
            ],
            [
                'submod_id'     => 30,
                'mod_id'        => 5,
                'submod_code'   => 'payslip',
                'submod_name'   => 'Payslip',
            ],
            [
                'submod_id'     => 31,
                'mod_id'        => 5,
                'submod_code'   => 'espt',
                'submod_name'   => 'E-SPT Report',
            ],
            [
                'submod_id'     => 32,
                'mod_id'        => 5,
                'submod_code'   => 'transfer-cash',
                'submod_name'   => 'Report Bank Transfer & Cash',
            ],
            [
                'submod_id'     => 33,
                'mod_id'        => 5,
                'submod_code'   => 'tax-form',
                'submod_name'   => 'Tax Form',
            ],
            [
                'submod_id'     => 34,
                'mod_id'        => 5,
                'submod_code'   => 'custom-report',
                'submod_name'   => 'Custom Report',
            ],
            [
                'submod_id'     => 35,
                'mod_id'        => 6,
                'submod_code'   => 'user',
                'submod_name'   => 'User',
            ],
            [
                'submod_id'     => 36,
                'mod_id'        => 6,
                'submod_code'   => 'group',
                'submod_name'   => 'Group',
            ],
        ]);
    }
}

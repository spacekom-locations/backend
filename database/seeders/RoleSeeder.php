<?php

namespace Database\Seeders;

use App\Models\Supervisor;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public const rootSupervisor = ['id' => '11111', 'name' => 'Root_Supervisor', 'guard_name' => 'supervisors'];

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(RoleSeeder::rootSupervisor);
    }
}

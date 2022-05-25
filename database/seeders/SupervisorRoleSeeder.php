<?php

namespace Database\Seeders;

use App\Models\Supervisor;
use Illuminate\Database\Seeder;

class SupervisorRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $defaultSupervisor = SupervisorSeeder::getDefaultSupervisor();

        $supervisor = Supervisor::find($defaultSupervisor['id']);

        $supervisor->assignRole(RoleSeeder::rootSupervisor['name']);
    }
}

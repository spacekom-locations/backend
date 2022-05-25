<?php

namespace Database\Seeders;

use App\Models\Supervisor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SupervisorSeeder extends Seeder
{

	public static function getDefaultSupervisor() {
		$defaultSupervisor = [
			'id' => 11111,
			'name' => 'Administrator',
			'user_name' => 'admin',
			'password' => Hash::make('Admin@123'),
			'status' => Supervisor::STATUS_ACTIVE,
			'notes' => 'This is the default root supervisor',
			'created_at' => now(),
		];

		return $defaultSupervisor ;
	}

	public static function getDefaultLowLevelSupervisor()
	{
		$lowLevelSupervisor = [
			'name' => 'Filan bin Illan',
			'user_name' => 'filan',
			'password' => Hash::make('filan@123'),
			'status' => Supervisor::STATUS_ACTIVE,
			'notes' => 'This is the default low level supervisor supervisor',
			'created_at' => now(),
		];

		return $lowLevelSupervisor;
	}


	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$rootSupervisor = new Supervisor(SupervisorSeeder::getDefaultSupervisor());
		$rootSupervisor->save();
	}
}

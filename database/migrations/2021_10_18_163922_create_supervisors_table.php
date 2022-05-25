<?php

use App\Models\Supervisor;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSupervisorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('supervisors', function (Blueprint $table) {
            // the valid statuses for the supervisor account
            $accountStatuses = [
                Supervisor::STATUS_ACTIVE, 
                Supervisor::STATUS_SUSPENDED,
            ];
            
            // the default status for the supervisor account
            $accountDefaultStatus = Supervisor::STATUS_ACTIVE;

            $table->id();
            $table->string('name');
            $table->string('user_name')->unique();
            $table->string('password', 255);
            $table->enum('status', $accountStatuses)->default($accountDefaultStatus);
            $table->text('notes')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('supervisors');
    }
}

<?php

use App\Models\LocationBookings;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('location_bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('location_id');
            $table->uuid('user_id');
            $table->json('dates');
            $table->json('addons');
            $table->string('project_name');
            $table->string('project_description')->nullable();
            $table->string('company_name');
            $table->tinyText('about_renter')->nullable();
            $table->json('system_fees')->nullable();
            $table->json('custom_fees')->nullable();
            $table->string('crew_size');
            $table->string('activity');
            $table->string('sub_activity');
            $table->boolean('is_custom_rate')->default(0);
            $table->json('pricing');
            $table->float('total_price');
            $table->enum('status', LocationBookings::getValidStatuses())->default(LocationBookings::getDefaultStatus());
            $table->string('cancellation_reason')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('locations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('location_bookings');
    }
};

<?php

use App\Models\Location;
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
        Schema::create('locations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->string('title')->nullable();
            $table->enum('status', Location::getValidStatus())->default(Location::getDefaultStatus());
            $table->string('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('street')->nullable();
            $table->string('apt_suite')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->enum('kind', Location::getValidKinds())->nullable();
            $table->json('types')->nullable();
            $table->unsignedTinyInteger('bedrooms_count')->nullable();
            $table->unsignedTinyInteger('bathrooms_count')->nullable();
            $table->unsignedSmallInteger('size')->nullable();
            $table->unsignedSmallInteger('lot_size')->nullable();
            $table->unsignedTinyInteger('main_floor_number')->nullable();
            $table->unsignedTinyInteger('parking_capacity')->nullable();
            $table->json('motor_home_parking_types')->nullable();
            $table->json('access_availability_types')->nullable();
            $table->boolean('has_parking_structure')->default(false);
            $table->json('amenities')->nullable();
            $table->json('styles')->nullable();
            $table->json('features')->nullable();
            $table->json('architectural_styles')->nullable();
            $table->json('bathrooms_types')->nullable();
            $table->json('ceiling_types')->nullable();
            $table->json('doors_types')->nullable();
            $table->json('exterior_types')->nullable();
            $table->json('floor_types')->nullable();
            $table->json('interior_types')->nullable();
            $table->json('kitchen_features')->nullable();
            $table->json('activities_types')->nullable();
            $table->json('walls_types')->nullable();
            $table->json('water_features')->nullable();
            $table->json('windows_types')->nullable();
            $table->text('description')->nullable();
            $table->json('crews_rules')->nullable();
            $table->json('availability_calendar')->nullable();
            $table->json('allowed_activities')->nullable();
            $table->unsignedSmallInteger('maximum_attendees_number')->nullable();
            $table->enum('minimum_rental_hours', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12])->nullable();
            $table->string('currency_code', 8)->nullable();
            $table->json('pricing')->nullable();
            $table->json('images')->nullable();
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('locations');
    }
};

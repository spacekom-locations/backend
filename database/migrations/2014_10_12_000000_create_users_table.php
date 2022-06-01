<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use phpDocumentor\Reflection\Types\Null_;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable()->default(null);
            $table->string('user_name')->unique()->nullable();
            $table->string('avatar')->nullable();
            $table->string('company')->nullable();
            $table->enum('category', User::validCategories())->nullable()->default(null);
            $table->mediumText('bio')->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->timestamp('phone_number_verified_at')->nullable()->default(null);
            $table->string('password');
            $table->enum('status', User::validStatuses())->default(User::defaultStatus());
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
        Schema::dropIfExists('users');
    }
};

<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //

        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
        \Cmgmyr\Messenger\Models\Models::setUserModel(\App\Models\User::class);
        \Cmgmyr\Messenger\Models\Models::setThreadModel(\App\Models\Messenger\Thread::class);
        \Cmgmyr\Messenger\Models\Models::setMessageModel(\App\Models\Messenger\Message::class);
        \Cmgmyr\Messenger\Models\Models::setParticipantModel(\App\Models\Messenger\Participant::class);
    }
}

<?php

namespace App\Models\Messenger;

use App\Traits\TraitUuid;
use Cmgmyr\Messenger\Models\Message as CmgmyrMessengerMessage;

class Message extends CmgmyrMessengerMessage
{
    use TraitUuid;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'messages';

    /**
     * The relationships that should be touched on save.
     *
     * @var array
     */
    protected $touches = ['thread'];

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['thread_id', 'user_id', 'body'];

    /**
     * The attributes that should be mutated to date's.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];
}

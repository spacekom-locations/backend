<?php

namespace App\Models\Messenger;

use App\Traits\TraitUuid;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Participant as CmgmyrMessengerParticipant;

class Participant extends CmgmyrMessengerParticipant
{
    use TraitUuid;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'participants';

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['thread_id', 'user_id', 'last_read'];

    /**
     * The attributes that should be mutated to date's.
     *
     * @var array
     */
    protected $dates = ['deleted_at', 'last_read'];

    public function setLastRead(Carbon $date_time)
    {
        $this->update(['last_read' => $date_time]);
    }
}

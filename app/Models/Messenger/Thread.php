<?php

namespace App\Models\Messenger;

use App\Models\Location;
use App\Traits\TraitUuid;
use Cmgmyr\Messenger\Models\Thread as CmgmyrMessengerThread;

class Thread extends CmgmyrMessengerThread
{
    use TraitUuid;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'threads';

    /**
     * The attributes that can be set with Mass Assignment.
     *
     * @var array
     */
    protected $fillable = ['location_id', 'booking_inputs', 'has_flexible_dates'];

    /**
     * The attributes that should be mutated to date's.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];


    /**
     * Returns the location object that created on this thread
     *
     * @return null|Location|\Illuminate\Database\Eloquent\Model
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id');
    }
}

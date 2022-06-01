<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\PersonalAccessToken as SanctumPersonalAccessToken;

class PersonalAccessToken extends SanctumPersonalAccessToken
{
    use HasFactory;

    protected $table = 'personal_access_tokens';

    public function tokenable()
    {
        return $this->morphTo('tokenable', "tokenable_type", "tokenable_id", "id");
    }
}
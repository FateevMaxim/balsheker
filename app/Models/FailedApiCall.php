<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedApiCall extends Model
{
    use HasFactory;

    protected $table = 'failed_api_calls';

    protected $fillable = [
        'user_id',
        'login',
        'track_code',
        'start_local',
        'target_local',
        'error_message',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

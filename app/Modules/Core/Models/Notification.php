<?php

namespace App\Modules\Core\Models;

use Illuminate\Notifications\DatabaseNotification;

class Notification extends DatabaseNotification
{
    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'module',
        'channel',
        'priority',
        'title',
        'action_url',
        'read_at',
    ];
}

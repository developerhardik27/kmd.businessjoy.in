<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    use HasFactory;

    protected $table = 'email_logs';

    protected $guarded = [];

    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get user full name from user ID
     */
    public static function getUserName($userId)
    {
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return 'Unknown';
        }

        // Check if firstname and lastname exist, use them; otherwise use name
        if (!empty($user->firstname) && !empty($user->lastname)) {
            return trim($user->firstname . ' ' . $user->lastname);
        }

        return $user->name ?? 'Unknown';
    }

    /**
     * Clean up old email logs - if more than 2000 records, delete the oldest 2000
     */
    public static function cleanupOldLogs()
    {
        $count = self::count();
        if ($count > 2000) {
            self::orderBy('id', 'asc')->limit(2000)->delete();
        }
    }
}

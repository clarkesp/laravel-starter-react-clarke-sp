<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    use HasFactory;

    protected $table = 'admin_activity_logs';

    protected $fillable = [
        'admin_user_id',
        'action',
        'resource_type',
        'resource_id',
        'description',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
        ];
    }

    public function adminUser()
    {
        return $this->belongsTo(AdminUser::class, 'admin_user_id');
    }

    public function scopeByUser($query, int $userId)
    {
        return $query->where('admin_user_id', $userId);
    }

    public function scopeByResource($query, string $resourceType, ?int $resourceId = null)
    {
        $query->where('resource_type', $resourceType);

        if ($resourceId) {
            $query->where('resource_id', $resourceId);
        }

        return $query;
    }

    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
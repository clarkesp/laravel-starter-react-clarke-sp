<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminPermission extends Model
{
    use HasFactory;

    protected $table = 'admin_permissions';

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'group',
    ];

    public function roles()
    {
        return $this->belongsToMany(AdminRole::class, 'admin_permission_role', 'admin_permission_id', 'admin_role_id')
            ->withTimestamps();
    }

    public function scopeByGroup($query, string $group)
    {
        return $query->where('group', $group);
    }
}
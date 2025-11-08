<?php

namespace App\Admin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminRole extends Model
{
    use HasFactory;

    protected $table = 'admin_roles';

    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    public function users()
    {
        return $this->belongsToMany(AdminUser::class, 'admin_role_user', 'admin_role_id', 'admin_user_id')
            ->withTimestamps();
    }

    public function permissions()
    {
        return $this->belongsToMany(AdminPermission::class, 'admin_permission_role', 'admin_role_id', 'admin_permission_id')
            ->withTimestamps();
    }

    public function givePermissionTo(AdminPermission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = AdminPermission::where('name', $permission)->firstOrFail();
        }

        $this->permissions()->syncWithoutDetaching($permission);
    }

    public function revokePermissionTo(AdminPermission|string $permission): void
    {
        if (is_string($permission)) {
            $permission = AdminPermission::where('name', $permission)->firstOrFail();
        }

        $this->permissions()->detach($permission);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions()->where('name', $permission)->exists();
    }
}
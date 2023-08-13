<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    public function permissions() {

        return $this->belongsToMany(Permission::class,'roles_permissions');

    }

    public function users() {

        return $this->belongsToMany(User::class,'users_roles');

    }

    /**
     * hasPermission function
     *
     * @param User $user
     * @param string $action
     * @return boolean
     */
    public function hasPermission(User $user, $action = 'all')
    {
        $hasPermission = $user->isSuperUser();

        return $hasPermission;
    }
}

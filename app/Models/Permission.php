<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    public function roles() {

        return $this->belongsToMany(Role::class,'roles_permissions');

    }

    public function users() {

        return $this->belongsToMany(User::class,'users_permissions');

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
        // $hasPermission = $user->isSuperUser();

        return true;
    }
}

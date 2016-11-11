<?php

namespace Panoscape\Permissionary;

/**
 * Permission trait
 */
trait Permission
{
    /**
     * The roles that belong to the permission
     *
     * @return RoleCollection
     */
    public function roles()
    {
        $pms = property_exists($this,'permissionary') ? $this->{'permissionary'} : 'pms';
        return $this->belongsToMany(config("$pms.models.role"), config("$pms.pivots.role_permission"), config("$pms.foreign_keys.permission"), config("$pms.foreign_keys.role"));
    }
}
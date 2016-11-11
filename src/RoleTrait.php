<?php

namespace Panoscape\Permissionary;

/**
 * Role trait
 */
trait Role
{
    /**
     * The users that belong to the role
     *
     * @return BelongsToMany
     */
    public function users()
    {
        $pms = property_exists($this,'permissionary') ? $this->{'permissionary'} : 'pms';
        return $this->belongsToMany(config("$pms.models.user"), config("$pms.pivots.user_role"), config("$pms.foreign_keys.role"), config("$pms.foreign_keys.user"));
    }

    /**
     * The roles that belong to the role
     *
     * @return Panoscape\Permissionary\Relationship
     */
    public function permissions()
    {
        $pms = property_exists($this,'permissionary') ? $this->{'permissionary'} : 'pms';
        return new Relationship($this->belongsToMany(config("$pms.models.permission"), 
            config("$pms.pivots.role_permission"), config("$pms.foreign_keys.role"), 
            config("$pms.foreign_keys.permission")));
    }
}

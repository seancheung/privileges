<?php

namespace Panoscape\Permissionary;

/**
 * User trait
 */
trait User
{
    /**
     * The roles that belong to the user
     *
     * @return Panoscape\Permissionary\Relationship
     */
    public function roles()
    {
        $pms = property_exists($this,'permissionary') ? $this->{'permissionary'} : 'pms';
        return new Relationship($this->belongsToMany(config("$pms.models.role"), config("$pms.pivots.user_role"), config("$pms.foreign_keys.user"), config("$pms.foreign_keys.role")));
    }

    /**
     * The roles that belong to the user
     *
     * @return Panoscape\Permissionary\Relationship
     */
    public function permissions()
    {
        $pms = property_exists($this,'permissionary') ? $this->{'permissionary'} : 'pms';
        
        $permissionClass = config("$pms.models.permission");        
        $roleClass = config("$pms.models.role");
        $role_permission = config("$pms.pivots.role_permission");
        $user_role = config("$pms.pivots.user_role");
        $permissionFk = config("$pms.foreign_keys.permission");
        $roleFk = config("$pms.foreign_keys.role");
        $userFk = config("$pms.foreign_keys.user");

        $permission = new $permissionClass;
        $role = new $roleClass;

        $query = $permissionClass::join($role_permission, "{$permission->getTable()}.{$permission->getKeyName()}", '=', "$role_permission.$permissionFk");
        $query->join($role->getTable(), "{$role->getTable()}.{$role->getKeyName()}", '=', "$role_permission.$roleFk");
        $query->join($user_role, "{$role->getTable()}.{$role->getKeyName()}", '=', "$user_role.$roleFk");
        $query->join($this->getTable(), "{$this->getTable()}.{$this->getKeyName()}", '=', "$user_role.$userFk");
        $query->where("{$this->getTable()}.{$this->getKeyName()}", '=', $this->getKey())->select("{$permission->getTable()}.*")->distinct();

        return new Relationship($query, $permission->getTable());
    }
}

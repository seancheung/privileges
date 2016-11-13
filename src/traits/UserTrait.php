<?php

namespace Panoscape\Privileges;

/**
 * User trait
 */
trait User
{
    /**
     * The groups that belong to the user
     *
     * @return Panoscape\Privileges\Relationship
     */
    public function __groups()
    {
        $profile = property_exists($this,'privileges_profile') ? $this->{'privileges_profile'} : 'privileges_profile';
        return new Relationship($this->belongsToMany(config("$profile.group.model"), config("$profile.user_group.table"), config("$profile.user_group.user_id"), config("$profile.user_group.group_id")));
    }

    /**
     * The privileges that belong to the user
     *
     * @return Panoscape\Privileges\Relationship
     */
    public function __privileges()
    {
        $profile = property_exists($this,'privileges_profile') ? $this->{'privileges_profile'} : 'privileges_profile';
        
        $privilege = config("$profile.privilege");
        $group = config("$profile.group");
        $user = config("$profile.user");
        $user_group = config("$profile.user_group");
        $group_privilege = config("$profile.group_privilege");

        $query = $privilege['model']::join($group_privilege['table'], "$privilege[table].$privilege[id]", '=', "$group_privilege[table].$group_privilege[privilege_id]");
        $query->join($group['table'], "$group[table].$group[id]", '=', "$group_privilege[table].$group_privilege[group_id]");
        $query->join($user_group['table'], "$group[table].$group[id]", '=', "$user_group[table].$user_group[group_id]");
        $query->join($user['table'], "$user[table].$user[id]", '=', "$user_group[table].$user_group[user_id]");
        $query->where("$user[table].$user[id]", '=', $this->getKey())->select("$privilege[table].*")->distinct();

        return new Relationship($query, $privilege['table']);
    }

    /**
     * Validate privileges
     *
     * @param string $pattern use ',' to separate, '!' for 'without', default for 'with'
     * @param string $column
     *
     * @example 'r=author,(editor,publisher);p=(insert,delete),(query,modify)'
     * 
     * @return bool
     */
    public function validate($pattern, $column = 'name')
    {
        $values = preg_split('/;\s*/', $pattern, 0, PREG_SPLIT_NO_EMPTY);

        $groups = null;
        $privileges = null;

        foreach($values as $key=>$value) {            
            if(starts_with($value, 'g=')) {
                $groups = $groups . substr($value, 2).'|';
            }
            else if(starts_with($value, 'p=')) {
                $privileges = $privileges . substr($value, 2).'|';
            }
        }
        
        if(!empty($groups) && !$this->__groups()->validate($groups, $column)) {
            return false;
        }

        if(!empty($privileges) && !$this->__privileges()->validate($privileges, $column)) {
            return false;
        }

        if(empty($groups) && empty($privileges)) {
            return false;
        }

        return true;
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        $profile = property_exists($this,'privileges_profile') ? $this->{'privileges_profile'} : 'privileges_profile';

        if($key == config("$profile.group.table")) return $this->__groups();

        if($key == config("$profile.privilege.table")) return $this->__privileges();

        return parent::__get($key);
    }

    /**
     * Handle dynamic method calls into the model.
     *
     * @param  string  $method
     * @param  array  $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        $profile = property_exists($this,'privileges_profile') ? $this->{'privileges_profile'} : 'privileges_profile';

        if($method == config("$profile.group.table")) return $this->__groups();

        if($method == config("$profile.privilege.table")) return $this->__privileges();

        return parent::__call($method, $parameters);
    }
}

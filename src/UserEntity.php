<?php

namespace Panoscape\Privileges;

/**
 * User trait
 */
trait UserEntity
{
    /**
     * Get the profile name
     *
     * @return string
     */
    public function profile()
    {
        return 'privileges_profile';
    }

    /**
     * The groups that belong to the user
     *
     * @return Panoscape\Privileges\Relationship
     */
    public function __groups()
    {
        return new Relationship($this->belongsToMany(config("{$this->profile()}.group.model"), config("{$this->profile()}.user_group.table"), config("{$this->profile()}.user_group.user_id"), config("{$this->profile()}.user_group.group_id")));
    }

    /**
     * The privileges that belong to the user
     *
     * @return Panoscape\Privileges\Relationship
     */
    public function __privileges()
    {       
        $privilege = config("{$this->profile()}.privilege");
        $group = config("{$this->profile()}.group");
        $user = config("{$this->profile()}.user");
        $user_group = config("{$this->profile()}.user_group");
        $group_privilege = config("{$this->profile()}.group_privilege");

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
        if($key == config("{$this->profile()}.group.table")) return $this->__groups();

        if($key == config("{$this->profile()}.privilege.table")) return $this->__privileges();

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
        if($method == config("{$this->profile()}.group.table")) return $this->__groups();

        if($method == config("{$this->profile()}.privilege.table")) return $this->__privileges();

        return parent::__call($method, $parameters);
    }
}

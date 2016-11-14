<?php

namespace Panoscape\Privileges;

/**
 * Group trait
 */
trait GroupEntity
{
    /**
     * The users that belong to the group
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function __users()
    {
        $profile = property_exists($this,'privileges_profile') ? $this->{'privileges_profile'} : 'privileges_profile';
        return $this->belongsToMany(config("$profile.user.model"), config("$profile.user_group.table"), config("$profile.user_group.group_id"), config("$profile.user_group.user_id"));
    }

    /**
     * The privileges that belong to the group
     *
     * @return Panoscape\Privileges\Relationship
     */
    public function __privileges()
    {
        $profile = property_exists($this,'privileges_profile') ? $this->{'privileges_profile'} : 'privileges_profile';
        return new Relationship($this->belongsToMany(config("$profile.privilege.model"), 
            config("$profile.group_privilege.table"), config("$profile.group_privilege.group_id"), 
            config("$profile.group_privilege.privilege_id")));
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

        if($key == config("$profile.privilege.table")) return $this->__privileges();

        if($key == config("$profile.user.table")) return $this->__users();

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

        if($method == config("$profile.privilege.table")) return $this->__privileges();

        if($method == config("$profile.user.table")) return $this->__users();

        return parent::__call($method, $parameters);
    }
}

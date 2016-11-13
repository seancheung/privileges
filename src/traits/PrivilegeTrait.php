<?php

namespace Panoscape\Privileges;

/**
 * Privilege trait
 */
trait Privilege
{
    /**
     * The groups that belong to the privilege
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function __groups()
    {
        $profile = property_exists($this,'privileges_profile') ? $this->{'privileges_profile'} : 'privileges_profile';
        return $this->belongsToMany(config("$profile.group.model"), config("$profile.group_privilege.table"), config("$profile.group_privilege.privilege_id"), config("$profile.group_privilege.group_id"));
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

        return parent::__call($method, $parameters);
    }
}
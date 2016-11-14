<?php

namespace Panoscape\Privileges;

/**
 * Privilege trait
 */
trait PrivilegeEntity
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
     * The groups that belong to the privilege
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function __groups()
    {
        return $this->belongsToMany(config("{$this->profile()}.group.model"), config("{$this->profile()}.group_privilege.table"), config("{$this->profile()}.group_privilege.privilege_id"), config("{$this->profile()}.group_privilege.group_id"));
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

        return parent::__call($method, $parameters);
    }
}
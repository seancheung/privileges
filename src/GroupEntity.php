<?php

namespace Panoscape\Privileges;

/**
 * Group trait
 */
trait GroupEntity
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
     * The users that belong to the group
     *
     * @return Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function __users()
    {
        return $this->belongsToMany(config("{$this->profile()}.user.model"), config("{$this->profile()}.user_group.table"), config("{$this->profile()}.user_group.group_id"), config("{$this->profile()}.user_group.user_id"));
    }

    /**
     * The privileges that belong to the group
     *
     * @return Panoscape\Privileges\Relationship
     */
    public function __privileges()
    {
        return new Relationship($this->belongsToMany(config("{$this->profile()}.privilege.model"), 
            config("{$this->profile()}.group_privilege.table"), config("{$this->profile()}.group_privilege.group_id"), 
            config("{$this->profile()}.group_privilege.privilege_id")));
    }

    /**
     * Dynamically retrieve attributes on the model.
     *
     * @param  string  $key
     * @return mixed
     */
    public function __get($key)
    {
        if($key == config("{$this->profile()}.privilege.table")) return $this->__privileges();

        if($key == config("{$this->profile()}.user.table")) return $this->__users();

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
        if($method == config("{$this->profile()}.privilege.table")) return $this->__privileges();

        if($method == config("{$this->profile()}.user.table")) return $this->__users();

        return parent::__call($method, $parameters);
    }
}

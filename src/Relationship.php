<?php

namespace Panoscape\Permissionary;

/**
* Relationship
*/
class Relationship
{
    /**
     * Query handler
     *
     * @var mixed
     */
    protected $query;
    
    protected $scope;

    /**
     * Create a new query collection instance
     *
     * @param mixed $query
     * @return void
     */
    function __construct($query, $scope = null)
    {
        $this->query = $query;
        if(!empty($scope))
        {
            $this->scope = "$scope.";
        }
    }

    function __call($method, $args)
    {
        return call_user_func_array([$this->query, $method], $args);
    }

    /**
     * Check value
     *
     * @param string $value
     * @param string $key
     * 
     * @return bool
     */
    function has($value, $key = 'name')
    {
        return $this->query->where("{$this->scope}$key", $value)->count() > 0;
    }

    /**
     * Check if any values exist
     *
     * @param array $values
     * @param string $key
     *
     * @return bool
     */
    function any($values, $key = 'name')
    {
        return $this->query->whereIn("{$this->scope}$key", $values)->count() > 0;
    }

    /**
     * Check if all values exist
     *
     * @param array $values
     * @param string $key
     *
     * @return bool
     */
    function all($values, $key = 'name')
    {
        return $this->query->whereIn("{$this->scope}$key", $values)->count() >= count($values);
    }

    /**
     * Get count
     *
     * @return integer
     */
    function count()
    {
        return $this->query->count();
    }
}
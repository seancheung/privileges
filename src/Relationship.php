<?php

namespace Panoscape\Privileges;

/**
* Relationship
*/
class Relationship
{
    /**
     * Relation
     *
     * @var mixed
     */
    protected $relation;
    
    /**
     * Query scope
     *
     * @var string
     */
    protected $scope;

    /**
     * Create a new Relationship instance
     *
     * @param mixed $relation
     * @return void
     */
    function __construct($relation, $scope = null)
    {
        $this->relation = $relation;
        if(!empty($scope))
        {
            $this->scope = "$scope.";
        }
    }

    public function __call($method, $args)
    {
        return call_user_func_array([$this->relation, $method], $args);
    }

    /**
     * Check value
     *
     * @param string $value
     * @param string $column
     * 
     * @return bool
     */
    public function has($value, $column = 'name')
    {
        return $this->relation->where("{$this->scope}$column", $value)->count() > 0;
    }

    /**
     * Check if any values exist
     *
     * @param array $values
     * @param string $column
     *
     * @return bool
     */
    public function any($values, $column = 'name')
    {
        return $this->relation->whereIn("{$this->scope}$column", $values)->count() > 0;
    }

    /**
     * Check if all values exist
     *
     * @param array $values
     * @param string $column
     *
     * @return bool
     */
    public function all($values, $column = 'name')
    {
        return $this->relation->whereIn("{$this->scope}$column", $values)->count() >= count($values);
    }

    /**
     * Match pattern
     *
     * @param string $pattern use ',' to separate, '()' for 'any', default for 'all', nested match is not supported
     * @param string $column
     *
     * @example 'insert,delete,(query,modify)' means 'with insert and delete and at least one of query or modify'
     * 
     * @return bool
     */
    public function validate($pattern, $column = 'name')
    {
        preg_match_all('/\([^\(\)]+\)|[^\(\)\|\s]+/', $pattern, $groups, PREG_SPLIT_NO_EMPTY);

        if(empty($groups)) return false;
        $groups = $groups[0];
        if(empty($groups)) return false;

        $all = [];
        $any = [];

        foreach($groups as $key=>$value) {
            if(starts_with($value, '(') && ends_with($value, ')')) {
                array_push($any, preg_split('/[\|\(\)\s]+/', $value, 0, PREG_SPLIT_NO_EMPTY));
            }
            else {
                array_push($all, $value);
            }
        }

        $count = 0;
        
        if(!empty($any)) {            
            foreach($any as $key=>$value) {
                $relation = clone $this->relation;
                if($relation->whereIn("{$this->scope}$column", $value)->count() > 0)
                    $count ++;
            }
        }

        if(!empty($all)) {
            $relation = clone $this->relation;
            $count += $relation->whereIn("{$this->scope}$column", $all)->count();
        }

        return $count >= count($groups);

    }
}
<?php

namespace Panoscape\Privileges;

interface Privileged
{
    public function __groups();

    public function __privileges();

    public function validate($pattern, $column = 'name');
}
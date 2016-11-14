# Privileges
Privilege and Group control for Laravel



## Installation

You can install this package via composer using this command:

```shell
composer require panoscape/privileges
```

Register service provider:

> config/app.php

```php
'providers' => [
    ...
    Panoscape\Privileges\PrivilegesServiceProvider::class,
];
```

If you need blade directives, also add this:

> config/app.php

```php
'providers' => [
    ...
    Panoscape\Privileges\PrivilegesBladeServiceProvider::class,
];
```

A middleware can also be registered:

> app/Http/Kernel.php

```php
protected $routeMiddleware = [
  ...
  'privileges' => \Panoscape\Privileges\Middleware\PrivilegesMiddleware::class,
];
```

Publish profile config:

```shell
php artisan vendor:publish --provider="Panoscape\Privileges\PrivilegesServiceProvider" --tag="profile"
```

Modify the published profile template to suit your application.

>  config/privileges_profile.php

```php
<?php

return [

    /*
    |--------------------------------------------------------------
    | User entity
    |--------------------------------------------------------------
    |
    */
    'user' => [

        /*
        |--------------------------------------------------------------
        | Model class
        |--------------------------------------------------------------
        |
        */
        'model' => '\App\User',

        /*
        |--------------------------------------------------------------
        | Table name
        |--------------------------------------------------------------
        |
        */
        'table' => 'users',

        /*
        |--------------------------------------------------------------
        | Primary key name in table
        |--------------------------------------------------------------
        |
        */
        'id' => 'id',
    ],

    /*
    |--------------------------------------------------------------
    | Group entity
    |--------------------------------------------------------------
    |
    */
    'group' => [

        /*
        |--------------------------------------------------------------
        | Model class
        |--------------------------------------------------------------
        |
        */
        'model' => '\App\Group',

        /*
        |--------------------------------------------------------------
        | Table name
        |--------------------------------------------------------------
        |
        */
        'table' => 'groups',

        /*
        |--------------------------------------------------------------
        | Primary key name in table
        |--------------------------------------------------------------
        |
        */
        'id' => 'id',
    ],

    /*
    |--------------------------------------------------------------
    | Privilege entity
    |--------------------------------------------------------------
    |
    */
    'privilege' => [

        /*
        |--------------------------------------------------------------
        | Model class
        |--------------------------------------------------------------
        |
        */
        'model' => '\App\Privilege',

        /*
        |--------------------------------------------------------------
        | Table name
        |--------------------------------------------------------------
        |
        */
        'table' => 'privileges',

        /*
        |--------------------------------------------------------------
        | Primary key name in table
        |--------------------------------------------------------------
        |
        */
        'id' => 'id',
    ],

    /*
    |--------------------------------------------------------------
    | User-Group pivot table
    |--------------------------------------------------------------
    |
    */
    'user_group' => [

        /*
        |--------------------------------------------------------------
        | Table name
        |--------------------------------------------------------------
        |
        */
        'table' => 'group_user',

        /*
        |--------------------------------------------------------------
        | User foreign key in table
        |--------------------------------------------------------------
        |
        */
        'user_id' => 'user_id',

        /*
        |--------------------------------------------------------------
        | Group foreign key in table
        |--------------------------------------------------------------
        |
        */
        'group_id' => 'group_id',
    ],

    /*
    |--------------------------------------------------------------
    | Group-Privilege pivot table
    |--------------------------------------------------------------
    |
    */
    'group_privilege' => [

        /*
        |--------------------------------------------------------------
        | Table name
        |--------------------------------------------------------------
        |
        */
        'table' => 'privilege_group',

        /*
        |--------------------------------------------------------------
        | Group foreign key in table
        |--------------------------------------------------------------
        |
        */
        'group_id' => 'group_id',

        /*
        |--------------------------------------------------------------
        | Privilege foreign key in table
        |--------------------------------------------------------------
        |
        */
        'privilege_id' => 'privilege_id',
    ]

];
```

Add `Panoscape\Privileges\Privilege\UserEntity` trait  to your user model, `Panoscape\Privileges\Privilege\GroupEntity` trait  to your group model, and `Panoscape\Privileges\Privilege\PrivilegeEntity` trait  to your privilege model.

If you have multiple privileges control flow or you prefer a different profile name, you may copy and modify the default profile template and rename it to something else, `admin_profile` for example. Then defile a method named `profile` in your related models and set them to the config name of your choice.

Here is an example of `Admin`, `Role`, `Permission`(instead of `User`,`Group`,`Privilege`):

> config/admin_profile.php

```php
<?php

return [
    'user' => [
        'model' => '\App\Admin',
        'table' => 'admins',
        'id' => 'id',
    ],
    'group' => [
        'model' => '\App\Role',
        'table' => 'roles',
        'id' => 'id',
    ],
    'privilege' => [
        'model' => '\App\Permission',
        'table' => 'permissions',
        'id' => 'id',
    ],
    'user_group' => [
        'table' => 'admin_role',
        'user_id' => 'admin_id',
        'group_id' => 'role_id',
    ],
    'group_privilege' => [
        'table' => 'permission_role',
        'group_id' => 'role_id',
        'privilege_id' => 'permission_id',
    ]
];
```

> app/Admin.php

```php
class Admin extends Authenticatable
{
  	...
    use \Panoscape\Privileges\UserEntity;
    
  	public function profile()
    {
        return 'admin_profile';
    }
}
```

> app\Role.php

```php
class Role extends Model
{
  	...
    use \Panoscape\Privileges\GroupEntity;
    
  	public function profile()
    {
        return 'admin_profile';
    }
}
```

> app\Permission.php

```php
class Permission extends Model
{
  	...
    use \Panoscape\Privileges\PrivilegeEntity;
    
  	public function profile()
    {
        return 'admin_profile';
    }
}
```



## Migration

This package does not provide any migrations or commands. You should create three required models/migrations and two pivot tables by yourself. The minimal requirements of table structures are listed in the profile template.



## Basic Usage

Access groups/privileges relationship of a user:

```php
$user->groups()->get();
$user->privileges()->get();
```

or via dynamic properties:

```php
$user->groups->get();
$user->privileges->get();
```

**If you have different entity names other than the default  `User`, `Group`, `Privilege`, You should access the relationships by the `table` values defined in your profile.**

Example of  `Admin`, `Role`, `Permission`:

```php
$admin->roles()->get();
$admin->roles->get();
$admin->permissions()->get();
$role->admins->get();
$role->permissions->get();
$permission->roles->get();
```



Group and Privilege validation

**has**:

```php
//returns true if target group is found on this user
$user->groups()->has('root');
```

**all**:

```php
//returns false unless all groups are found on this user
$user->groups()->all(['editor', 'author', 'subscriber']);
```

**any**:

```php
//returns true as long as any of these groups are found on this user
$user->groups()->any(['editor', 'author', 'subscriber']);
```



Instead of the default `name` column checking, you may specify which column to check:

```php
//check name column by default
$user->groups()->has('root');
//check fullname column instead
$user->groups()->has('Root Administrator', 'fullname');
//check id column instead
$user->groups()->any([1, 3, 5], 'id');
```



**validate**:

With this method you can do complex checking

*all*:

```php
$user->groups()->validate('root|author|subscriber')
```

equivalent to

```php
$user->groups()->all(['editor', 'author', 'subscriber'])
```

*any*:

```php
$user->groups()->validate('(root|author|subscriber)')
```

equivalent to

```php
$user->groups()->any(['editor', 'author', 'subscriber'])
```

*all* + *any*:

```php
$user->privileges()->validate('query|(delete|insert)|update')
```

equivalent to

```php
$user->privileges()->all(['query', 'update']) && $user->privileges()->any(['delete', 'insert'])
```

*group*:

```php
$user->validate('g=root|(author|subscriber)')
```

equivalent to

```php
$user->groups()->all(['root']) && $user->groups()->any(['author', 'subscriber'])
```

*privilege*:

```php
$user->validate('p=query|(delete|insert)|update')
```

equivalent to

```php
$user->privileges()->all(['query', 'update']) && $user->privileges()->any(['delete', 'insert'])
```

*group* + *privilege*:

```php
$user->validate('g=root|(author|subscriber);p=query|(delete|insert)|update')
```

equivalent to

```php
$user->groups()->all(['root']) && $user->groups()->any(['author', 'subscriber'])
  && $user->privileges()->all(['query', 'update']) && $user->privileges()->any(['delete', 'insert'])
```

Column specification is also available:

```php
$user->validate('g=1|(3|5);p=1|(2|10)|3', 'id')
```



**attach/detach/sync**

Relationships attaching/detaching/syncing are in the same way as Laravel's Eloquent Relationships Do. See  [ManyToMany Relationships](https://laravel.com/docs/5.3/eloquent-relationships#updating-many-to-many-relationships)



## Middleware

If you have registered the middleware, you can add it to any routes you'd like to guard with it.

```php
Route::get('/pages', 'PageController@index')->middleware('privileges:g=editor|(author|subscriber);p=query|(delete|insert)|update');
```



## Balde

If you have registered the blade service provider, you may guard your blade codes with `@validate` , `@group` and `@privilege`.

Your user entity should implement `Panoscape\Privileges\Privileged` interface before using these blade directives.

```php
class Admin extends Authenticatable implements \Panoscape\Privileges\Privileged
{
  	...
    use \Panoscape\Privileges\UserEntity;
}
```

Blade directives:

```php
@group('root')
  <button>
  	...
  </button>
 @endgroup
  
 @group('1', 'id')
  <button>
  	...
  </button>
 @endgroup
  
 @privilege('edit_users')
  <button>
  	...
  </button>
 @endprivilege
  
 @validate('g=(root|editor);p=edit_users')
  <button>
  	...
  </button>
 @endvalidate
```


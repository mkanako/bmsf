<?php

namespace Cc\Labems\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    public $timestamps = false;
    protected $visible = ['route_path'];
    protected $table = LABEMS_ENTRY . '_users_permissions';
}

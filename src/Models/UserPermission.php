<?php

namespace Cc\Bmsf\Models;

use Illuminate\Database\Eloquent\Model;

class UserPermission extends Model
{
    public $timestamps = false;
    protected $visible = ['route_path'];
    protected $table = BMSF_ENTRY . '_users_permissions';
}

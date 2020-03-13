<?php

namespace Modules\LaravelCore\Entities;

use Modules\LaravelCore\Entities\Permission;
use App\Models\TechlifyModel;
use App\User;

class RolePermission extends TechlifyModel
{

    protected $table = "permission_role";
}

<?php

namespace Modules\LaravelCore\Entities;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{

    const APP_ADMIN = 1;
    const CLIENT_ADMIN = 2;
    const CLIENT_USER = 3;

    public static function getUserTypeIdFromCode($code)
    {
        switch (strtolower($code)) {
            case "app-admin":
                return self::APP_ADMIN;
            case "client-admin":
                return self::CLIENT_ADMIN;
            default:
            case "client-user":
                return self::CLIENT_USER;
        }

        return null;
    }
}

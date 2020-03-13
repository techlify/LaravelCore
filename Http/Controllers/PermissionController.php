<?php

namespace Modules\LaravelCore\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\LaravelCore\Entities\Permission;

class PermissionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filters = request([
            "module_code"
        ]);

        $items = Permission::filter($filters)
            ->orderBy("label")
            ->get();

        return ["data" => $items];
    }
}

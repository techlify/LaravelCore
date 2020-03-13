<?php
namespace Modules\LaravelCore\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\LaravelCore\Entities\ModulePackage;

class ModulePackageController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $filters = request([
            'module_id',
            'sort_by',
            'num_item',
        ]);

        $modulePackages = ModulePackage::filter($filters)
            ->with('module')
            ->get();

        return ['data' => $modulePackages];
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $rules = [
            'module_id' => 'required|exists:modules,id',
            'code'     => 'required',
            'name' => 'required',
            'features' => 'required',
            'monthly_cost' => 'required|numeric'
        ];

        $this->validate(request(), $rules);

        $modulePackage = new ModulePackage();
        $modulePackage->name = request('name');
        $modulePackage->module_id = request('module_id');
        $modulePackage->code = request('code');
        $modulePackage->features = request('features');
        $modulePackage->monthly_cost = request('monthly_cost');

        if (!$modulePackage->save()) {
            return response()->json(['error' => 'Module addition failed.'], 422);
        }

        return ['item' => $modulePackage];
    }
}

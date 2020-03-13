<?php
namespace Modules\LaravelCore\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\Client\Entities\ClientSubscription;
use Modules\Client\Entities\ClientSubscriptionStatus;
use Modules\LaravelCore\Entities\Module;
use Modules\User\Entities\UserType;

class ModuleController extends Controller
{

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $filters = request([
            'name',
            'sort_by',
            'num_item',
            'not_in_user_id',
            'current_client_not_subscribed',
        ]);

        $parts = [];
        if (isset($filters['num_items']) && "" != trim($filters['num_items'])) {
            $parts = explode("|", $filters['num_items']);
        }

        $modules = Module::filter($filters)
            ->with('creator')
            ->with('packages')
            ->with('users')
            ->paginate(count($parts) ? $parts[0] : 25, ['*'], 'page', count($parts) > 1 ? $parts[1] : 1);

        return $modules;
    }

    /**
     * Get the set of modules enabled for the currently logged in client
     */
    public function getCurrentClientModules()
    {

        if (auth()->user()->user_type_id == UserType::CLIENT_USER) {
            $modules = Module::whereIn('id', function ($query) {
                $query->select('module_id')
                    ->from(with(new ClientSubscription)->getTable())
                    ->where('client_id', auth()->user()->client_id)
                    ->where('status_id', ClientSubscriptionStatus::STATUS_ACTIVE);
            })->where(function ($subQuery) {
                $subQuery->whereHas('users', function ($q) {
                    $q->where('client_id', auth()->user()->client_id);
                    $q->where('user_id', auth()->id());
                })->orDoesntHave('users');
            })->get();
        } else {
            $modules = Module::whereIn('id', function ($query) {
                $query->select('module_id')
                    ->from(with(new ClientSubscription)->getTable())
                    ->where('client_id', auth()->user()->client_id)
                    ->where('status_id', ClientSubscriptionStatus::STATUS_ACTIVE);
            })->get();
        }

        return ['data' => $modules];
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required',
            'code' => 'required',
        ];

        $this->validate(request(), $rules);

        $module = new Module();
        $module->name = request('name');
        $module->code = request('code');
        $module->description = request('description', '');
        $module->creator_id = auth()->id();

        if (!$module->save()) {
            return response()->json(['error' => 'Module addition failed.'], 422);
        }

        return ['item' => $module];
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {
        $module = Module::find($id);

        if (null == $module) {
            return response()->json(['error' => 'Invalid Module data sent.'], 422);
        }

        $module->load('creator');

        return ['item' => $module];
    }

    /**
     * Update the specified resource in storage.
     * @param int $id
     * @return Response
     */
    public function update($id)
    {
        $module = Module::find($id);

        if (null == $module) {
            return response()->json(['error' => 'Invalid Module data sent.'], 422);
        }

        $rules = [
            'name' => 'required',
            'code' => 'required',
        ];

        $this->validate(request(), $rules);

        $module->name = request('name');
        $module->code = request('code');
        $module->description = request('description', '');

        if (!$module->save()) {
            return response()->json(['error' => 'Module updation failed.'], 422);
        }

        return ['item' => $module];
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $module = Module::find($id);

        if (null == $module) {
            return response()->json(['error' => 'Invalid Module data sent.'], 422);
        }

        if (!$module->delete()) {
            return response()->json(['error' => 'Module deletion failed.'], 422);
        }

        return ['item' => $module];
    }

    public function enable($id)
    {
        $module = Module::find($id);

        if (!$module) {
            return response()->json(['error' => "No such module exists. "], 422);
        }

        $module->enabled = true;
        if (!$module->save()) {
            return response()->json(['error' => "Failed to enable the module. "], 422);
        }

        return ["item" => $module];
    }

    public function disable($id)
    {
        $module = Module::find($id);

        if (!$module) {
            return response()->json(['error' => "No such module exists. "], 422);
        }

        $module->enabled = false;
        if (!$module->save()) {
            return response()->json(['error' => "Failed to disable the module. "], 422);
        }

        return ["item" => $module];
    }
}

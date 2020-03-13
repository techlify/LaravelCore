<?php

namespace Modules\LaravelCore\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\LaravelCore\Entities\ClientSubscription;
use Modules\LaravelCore\Entities\ClientSubscriptionStatus;
use Modules\LaravelCore\Events\ClientSubscriptionCreatedEvent;
use Modules\LaravelCore\Entities\ModulePackage;
use Modules\LaravelCore\Entities\Module;
use Carbon\Carbon;

class ClientSubscriptionController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filters = request(['client', 'package_id', 'status_id', 'sort_by', 'num_items', 'client_id']);

        $parts = [];
        if (isset($filters['num_items']) && "" != trim($filters['num_items'])) {
            $parts = explode("|", $filters['num_items']);
        }

        $subscriptions = ClientSubscription::filter($filters)
            ->with('package.module')
            ->with('client')
            ->with('status')
            ->paginate(count($parts) ? $parts[0] : 25, ['*'], 'page', count($parts) > 1 ? $parts[1] : 1);

        return $subscriptions;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'monthly_cost' => 'required|numeric',
            'package_id' => 'required|exists:module_packages,id',
            'start_date' => 'required',
            'client_id' => 'required|exists:clients,id',
        ];

        $this->validate(request(), $rules);

        $package = ModulePackage::find(request('package_id'));
        
        $module = Module::find(request('module_id'));

        $subscription = new ClientSubscription();
        $subscription->client_id = request('client_id');
        $subscription->package_id = request('package_id');
        $subscription->module_id = $package->module_id;
        $subscription->monthly_cost = request('monthly_cost');
        $subscription->start_date = explode('T', request('start_date'))[0];
        $subscription->is_trial = request('is_trial', false);
        $subscription->status_id = ClientSubscriptionStatus::STATUS_ACTIVE;

        if($subscription->is_trial) { 
            $subscription->trial_start_date = Carbon::now();
            $subscription->trial_end_date = Carbon::now()->addDays($module->trial_duration);
        }

        if (!$subscription->save()) {
            return response()->json(['error' => 'Failed to add subscription'], 422);
        }
        $subscription->module;
        $subscription->package;
        $subscription->client;

        /* Lets setup our event */
        event(new ClientSubscriptionCreatedEvent($subscription));

        return ['item' => $subscription];
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subscription = ClientSubscription::find($id);

        if (null == $subscription) {
            return response()->json(['error' => 'Invlaid client subscription data sent'], 422);
        }

        $subscription->load('package');
        $subscription->load('client');
        $subscription->load('status');

        return ['item' => $subscription];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $rules = [
            'monthly_cost' => 'required|numeric',
            'package_id' => 'required|exists:module_packages,id',
            'start_date' => 'required',
        ];

        $this->validate(request(), $rules);

        $subscription = ClientSubscription::find($id);

        if (null == $subscription) {
            return response()->json(['error' => 'Invlaid client subscription data sent'], 422);
        }

        $package = ModulePackage::find(request('package_id'));

        $this->validate(request(), $rules);

        $subscription->package_id = request('package_id');
        $subscription->module_id = $package->module_id;
        $subscription->monthly_cost = request('monthly_cost');
        $subscription->start_date = explode('T', request('start_date'))[0];

        if (!$subscription->save()) {
            return response()->json(['error' => 'Failed to update subscription'], 422);
        }

        return ['item' => $subscription];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subscription = ClientSubscription::find($id);

        if (null == $subscription) {
            return response()->json(['error' => 'Invlaid client subscription data sent'], 422);
        }

        if (!$subscription->delete()) {
            return response()->json(['error' => 'Client Subscription deletion failed'], 422);
        }
        return ['item' => $subscription];
    }

    public function trialSubscription(Request $request)
    {
        $rules = [
            'module_id' => 'required|exists:modules,id',
            'package_id' => 'required|exists:module_packages,id'
        ];

        $this->validate(request(), $rules);

        $module = Module::find(request('module_id'));

        $subscription = new ClientSubscription();
        $subscription->client_id = auth()->user()->client_id;
        $subscription->package_id = request('package_id');
        $subscription->module_id = request('module_id');
        $subscription->monthly_cost = 0;
        $subscription->start_date = Carbon::now();
        $subscription->status_id = ClientSubscriptionStatus::STATUS_ACTIVE;
        $subscription->is_trial = true;
        $subscription->trial_start_date = Carbon::now();
        $subscription->trial_end_date = Carbon::now()->addDays($module->trial_duration);

        if (!$subscription->save()) {
            return response()->json(['error' => 'Failed to add subscription'], 422);
        }
        $subscription->module;
        $subscription->package;
        $subscription->client;

        /* Lets setup our event */
        event(new ClientSubscriptionCreatedEvent($subscription));

        return ['item' => $subscription];
    }
}

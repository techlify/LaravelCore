<?php
namespace Modules\LaravelCore\Http\Controllers;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Modules\LaravelCore\Emails\WelcomeClientMail;
use Modules\LaravelCore\Entities\Client;
use Modules\LaravelCore\Events\ClientCreatedEvent;
use Modules\Module\Entities\Module;
use Modules\WorkTask\Entities\WorkTask;

class ClientController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filters = request(['name', 'address', 'phone', 'email', 'sort_by', 'num_items']);

        $parts = [];
        if (isset($filters['num_items']) && "" != trim($filters['num_items'])) {
            $parts = explode("|", $filters['num_items']);
        }

        $clients = Client::filter($filters)
            ->with('creator')
            ->paginate(count($parts) ? $parts[0] : 25, ['*'], 'page', count($parts) > 1 ? $parts[1] : 1);

        return $clients;
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
            'name' => 'required',
        ];

        $this->validate(request(), $rules);

        $client = new Client();
        $client->name = request('name');
        $client->logo = request('logo', '');
        $client->letterhead_image = request('letterhead_image', '');
        $client->phone = request('phone', '');
        $client->address = request('address', '');
        $client->email = request('email', '');
        $client->tin = request('tin', '');
        $client->creator_id = auth()->id();

        if (!$client->save()) {
            return response()->json(['error' => 'Failed to add client'], 422);
        }

        /* Lets setup our event */
        event(new ClientCreatedEvent($client));

        $modules = Module::get();
        Mail::to($client->email)->queue(new WelcomeClientMail($client, $modules));

        return ['item' => $client];
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client = Client::find($id);

        if (null == $client) {
            return response()->json(['error' => 'Invalid Client data sent'], 422);
        }

        $client->load('creator');

        return ['item' => $client];
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function edit(Client $client)
    {
        //
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
        $client = Client::find($id);

        if (null == $client) {
            return response()->json(['error' => 'Invlaid client data sent'], 422);
        }
        $rules = [
            'name' => 'required',
        ];

        $this->validate(request(), $rules);

        $client->name = request('name');
        $client->logo = request('logo', '');
        $client->letterhead_image = request('letterhead_image', '');
        $client->phone = request('phone', '');
        $client->address = request('address', '');
        $client->email = request('email', '');
        $client->tin = request('tin', '');

        if (!$client->save()) {
            return response()->json(['error' => 'Failed to update client'], 422);
        }

        return ['item' => $client];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $client = Client::find($id);

        if (null == $client) {
            return response()->json(['error' => 'Invalid Client data sent'], 422);
        }

        if (!$client->delete()) {
            return response()->json(['error' => 'Client deletion failed'], 422);
        }
        return ['item' => $client];
    }

    public function clientWorkTaskSummary()
    {
        $filters = request(['name', 'address', 'phone', 'email', 'sort_by', 'num_items']);

        $parts = [];
        if (isset($filters['num_items']) && "" != trim($filters['num_items'])) {
            $parts = explode("|", $filters['num_items']);
        }

        $clients = Client::filter($filters)
            ->with('creator')
            ->paginate(count($parts) ? $parts[0] : 25, ['*'], 'page', count($parts) > 1 ? $parts[1] : 1);

        $clients->getCollection()->transform(function ($client) {
            $client->tasks_created_in_the_past_week = WorkTask::where('client_id', $client->id)
                ->whereBetween('created_at', [Carbon::now()->subDays(6), Carbon::now()])
                ->count();
            $client->tasks_completed_in_the_past_week = WorkTask::where('client_id', $client->id)
                ->whereBetween('completed_at', [Carbon::now()->subDays(6), Carbon::now()])
                ->count();
            $client->tasks_created_in_the_past_month = WorkTask::where('client_id', $client->id)
                ->whereBetween('created_at', [Carbon::now()->subDays(30), Carbon::now()])
                ->count();
            $client->tasks_completed_in_the_past_month = WorkTask::where('client_id', $client->id)
                ->whereBetween('completed_at', [Carbon::now()->subDays(30), Carbon::now()])
                ->count();
            $client->tasks_created_in_the_past_quarter = WorkTask::where('client_id', $client->id)
                ->whereBetween('created_at', [Carbon::now()->subQuarter(), Carbon::now()])
                ->count();
            $client->tasks_completed_in_the_past_quarter = WorkTask::where('client_id', $client->id)
                ->whereBetween('completed_at', [Carbon::now()->subQuarter(), Carbon::now()])
                ->count();
            $client->tasks_created_in_the_past_half_year = WorkTask::where('client_id', $client->id)
                ->whereBetween('created_at', [Carbon::now()->subQuarters(2), Carbon::now()])
                ->count();
            $client->tasks_completed_in_the_past_half_year = WorkTask::where('client_id', $client->id)
                ->whereBetween('completed_at', [Carbon::now()->subQuarters(2), Carbon::now()])
                ->count();
            $client->tasks_created_in_the_past_year = WorkTask::where('client_id', $client->id)
                ->whereBetween('created_at', [Carbon::now()->subYear(), Carbon::now()])
                ->count();
            $client->tasks_completed_in_the_past_year = WorkTask::where('client_id', $client->id)
                ->whereBetween('completed_at', [Carbon::now()->subYear(), Carbon::now()])
                ->count();
            $client->total_tasks_created = WorkTask::where('client_id', $client->id)
                ->count();
            $client->total_tasks_completed = WorkTask::where('client_id', $client->id)
                ->whereNotNull('completed_at')
                ->count();
            return $client;
        });

        return $clients;
    }
}

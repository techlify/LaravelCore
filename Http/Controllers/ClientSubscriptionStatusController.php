<?php
namespace Modules\LaravelCore\Http\Controllers;

use Modules\LaravelCore\Entities\ClientSubscriptionStatus;
use App\Http\Controllers\Controller;

class ClientSubscriptionStatusController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filters = request(['title', 'sort_by', 'num_items']);

        $clients = ClientSubscriptionStatus::filter($filters)
            ->get();

        return ['data' => $clients];
    }
}

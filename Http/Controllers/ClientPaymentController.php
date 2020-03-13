<?php
namespace Modules\LaravelCore\Http\Controllers;

use Modules\LaravelCore\Entities\ClientPayment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
class ClientPaymentController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filters = request(['sort_by', 'num_items']);

        $payments = ClientPayment::filter($filters)
            ->with('creator')
            ->with('subscription')
            ->get();

        return ['data' => $payments];
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
            'subscription_id' => 'required|exists:client_subscriptions,id',
            'amount_paid'     => 'required|numeric',
            'amount_due'     => 'required|numeric',
            'paid_on'         => 'required'
        ];

        $this->validate(request(), $rules);
        
        $payment = new ClientPayment();
        $payment->subscription_id = request('subscription_id');
        $payment->amount_paid = request('amount_paid');
        $payment->amount_due = request('amount_due');
        $payment->paid_on = explode('T', request('paid_on'))[0];
        $payment->creator_id = auth()->id();

        if (!$payment->save()) {
            return response()->json(['error' => 'Failed to add client payment'], 422);
        }

        return ['item' => $payment];
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
        $payment = ClientPayment::find($id);

        if (null == $payment) {
            return response()->json(['error' => 'Invlaid client payment data sent'], 422);
        }
        $rules = [
            'amount_paid'     => 'required|numeric',
            'amount_due'     => 'required|numeric',
            'paid_on'         => 'required'
        ];

        $this->validate(request(), $rules);
        
        $payment->amount_paid = request('amount_paid');
        $payment->amount_due = request('amount_due');
        $payment->paid_on = explode('T', request('paid_on'))[0];

        if (!$payment->save()) {
            return response()->json(['error' => 'Failed to update client payment'], 422);
        }

        return ['item' => $payment];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $payment = ClientPayment::find($id);

        if (null == $payment) {
            return response()->json(['error' => 'Invlaid client payment data sent'], 422);
        }

        if (!$payment->delete()) {
            return response()->json(['error' => 'Client Payment deletion failed'], 422);
        }
        return ['item' => $payment];
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function setPaid($id)
    {
        $payment = ClientPayment::find($id);

        if (null == $payment) {
            return response()->json(['error' => 'Invlaid client payment data sent'], 422);
        }

        
        $payment->paid = true;

        if (!$payment->save()) {
            return response()->json(['error' => 'Failed to update client payment'], 422);
        }

        return ['item' => $payment];
    }
}

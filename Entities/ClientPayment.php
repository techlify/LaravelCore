<?php
namespace Modules\LaravelCore\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Modules\LaravelCore\Entities\ClientSubscription;

class ClientPayment extends Model
{

    use \Illuminate\Database\Eloquent\SoftDeletes;
    use \TechlifyInc\LaravelModelLogger\Traits\LoggableModel;

    const TYPE = "client_payment";

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function subscription()
    {
        return $this->hasOne(ClientSubscription::class, 'id', 'subscription_id')->with('client');
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['sort_by']) && "" != trim($filters['sort_by'])) {
            $sort = explode("|", $filters['sort_by']);
            $query->orderBy($sort[0], $sort[1]);
        }
        if (isset($filters['num_items']) && is_numeric($filters['num_items'])) {
            $query->limit($filters['num_items']);
        }
    }
}

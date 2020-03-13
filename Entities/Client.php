<?php
namespace Modules\LaravelCore\Entities;

use Illuminate\Database\Eloquent\Model;
use App\User;
use Modules\LaravelCore\Entities\ClientSubscription;
use Modules\Payroll\Entities\CompanyInformation;
class Client extends Model
{

    use \Illuminate\Database\Eloquent\SoftDeletes;
    // use \TechlifyInc\LaravelModelLogger\Traits\LoggableModel;

    const TYPE = "client";

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function subscriptions()
    {
        return $this->hasMany(ClientSubscription::class, 'client_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'client_id', 'id');
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['name']) && "" != trim($filters['name'])) {
            $query->where('name', 'LIKE', "%" . $filters['name'] . "%");
        }
        if (isset($filters['phone']) && "" != trim($filters['phone'])) {
            $query->where('phone', 'LIKE', "%" . $filters['phone'] . "%");
        }
        if (isset($filters['address']) && "" != trim($filters['address'])) {
            $query->where('address', 'LIKE', "%" . $filters['address'] . "%");
        }
        if (isset($filters['email']) && "" != trim($filters['email'])) {
            $query->where('email', 'LIKE', "%" . $filters['email'] . "%");
        }
        if (isset($filters['sort_by']) && "" != trim($filters['sort_by'])) {
            $sort = explode("|", $filters['sort_by']);
            $query->orderBy($sort[0], $sort[1]);
        }
        if (isset($filters['num_items']) && is_numeric($filters['num_items'])) {
            $query->limit($filters['num_items']);
        }
    }
}

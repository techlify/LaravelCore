<?php
namespace Modules\LaravelCore\Entities;

use App\Models\TechlifyModel;
use Carbon\Carbon;
use Modules\LaravelCore\Entities\Client;
use Modules\LaravelCore\Entities\ClientSubscriptionStatus;
use Modules\LaravelCore\Entities\Module;
use Modules\LaravelCore\Entities\ModulePackage;

class ClientSubscription extends TechlifyModel
{

    use \Illuminate\Database\Eloquent\SoftDeletes;
    // use \TechlifyInc\LaravelModelLogger\Traits\LoggableModel;

    const TYPE = "client_subscription";

    protected $appends = [
        'is_expired',
        'days_remaining',
    ];

    public function getIsExpiredAttribute()
    {
        return $this->trial_end_date < Carbon::now()->toDateString();
    }

    public function getDaysRemainingAttribute()
    {
        return Carbon::parse($this->trial_end_date)->diffInDays(Carbon::now());
    }

    public function package()
    {
        return $this->hasOne(ModulePackage::class, 'id', 'package_id');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'id');
    }

    public function status()
    {
        return $this->hasOne(ClientSubscriptionStatus::class, 'id', 'status_id');
    }

    public function client()
    {
        return $this->hasOne(Client::class, 'id', 'client_id');
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['client']) && "" != trim($filters['client'])) {
            $query->whereHas('client', function ($q) use ($filters) {
                $q->where('name', 'LIKE', '%' . $filters['client'] . '%');
            })->get();
        }
        if (isset($filters['client_id']) && "" != trim($filters['client_id']) && ($filters['client_id']) > 0) {
            $query->whereHas('client', function ($q) use ($filters) {
                $q->where('client_id', '=', $filters['client_id']);
            })->get();
        }
        if (isset($filters['package_id']) && "" != trim($filters['package_id']) && ($filters['package_id']) > 0) {
            $query->whereHas('package', function ($q) use ($filters) {
                $q->where('package_id', '=', $filters['package_id']);
            })->get();
        }
        if (isset($filters['status_id']) && "" != trim($filters['status_id']) && ($filters['status_id']) > 0) {
            $query->whereHas('status', function ($q) use ($filters) {
                $q->where('status_id', '=', $filters['status_id']);
            })->get();
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

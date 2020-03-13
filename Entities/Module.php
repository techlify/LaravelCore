<?php
namespace Modules\LaravelCore\Entities;

use App\User;
use Illuminate\Database\Eloquent\Model;
use Modules\LaravelCore\Entities\ModulePackage;
use Modules\User\Entities\UserType;

class Module extends Model
{

    use \Illuminate\Database\Eloquent\SoftDeletes;

    const TYPE = "module";

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }

    public function packages()
    {
        return $this->hasMany(ModulePackage::class, "module_id", "id");
    }

    public function users()
    {
        return $this->hasMany(ModuleUser::class, "module_id", "id");
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['name']) && "" != trim($filters['name'])) {
            $query->where('name', 'LIKE', "%" . $filters['name'] . "%");
        }
        if (isset($filters['sort_by']) && "" != trim($filters['sort_by'])) {
            $sort = explode("|", $filters['sort_by']);
            $query->orderBy($sort[0], $sort[1]);
        }
        if (isset($filters['num_items']) && is_numeric($filters['num_items'])) {
            $query->limit($filters['num_items']);
        }
        if (isset($filters['code'])) {
            $query->where('code', $filters['code']);
        }
        if (isset($filters['not_in_user_id']) && "" != trim($filters['not_in_user_id'])) {
            $query->whereNotIn('id', function ($query) use ($filters) {
                $query->select('module_id')->from('module_users')->where('user_id', $filters['not_in_user_id']);
            });
        }

        if (isset($filters['current_client_not_subscribed']) && $filters['current_client_not_subscribed']) {
            $query->whereNotIn('id', function ($query) {
                $query->select('module_id')->from('client_subscriptions')->where('client_id', auth()->user()->client_id);
            });
        }

        if (auth()->check() && auth()->user()->user_type_id != UserType::BIS_ADMIN) {
            $query->where('enabled', true);
        }
        if (!auth()->check()) {
            $query->where('enabled', true);
        }
    }
}

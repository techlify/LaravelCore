<?php
namespace Modules\LaravelCore\Entities;

use Illuminate\Database\Eloquent\Model;

class ClientSubscriptionStatus extends Model
{
    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 2;

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['title']) && "" != trim($filters['title'])) {
            $query->where('title', 'LIKE', $filters['title']);
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

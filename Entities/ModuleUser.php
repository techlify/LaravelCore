<?php

namespace Modules\LaravelCore\Entities;

use App\User;
use Modules\Client\Entities\Client;
use App\Models\TechlifyModel;
use Modules\WorkTask\Entities\EmailOption;

class ModuleUser extends TechlifyModel
{

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id', 'id');
    }
    
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id')->with('roles');
    }

    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'id');
    }

    public function createEmailOption() {
        return EmailOption::createEmailOption($this->user_id);
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['name']) && "" != trim($filters['name'])) {
            $query->where('name', 'LIKE', $filters['name']);
        }

        if (isset($filters['module_code']) && "" != trim($filters['module_code'])) {
            $query->whereHas('module', function ($q) use ($filters) {
                $q->where('code', $filters['module_code']);
            })->get();
        }

        if (isset($filters['user_id']) && is_numeric($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['sort_by']) && "" != trim($filters['sort_by'])) {
            $sort = explode("|", $filters['sort_by']);
            $query->orderBy($sort[0], $sort[1]);
        }

        if (isset($filters['num_items']) && is_numeric($filters['num_items'])) {
            $query->limit($filters['num_items']);
        }

        if (isset($filters['client_id'])) {
            $query->where('client_id', $filters['client_id']);
        }
    }
}

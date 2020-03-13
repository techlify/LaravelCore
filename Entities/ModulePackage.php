<?php
namespace Modules\LaravelCore\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\LaravelCore\Entities\Module;

class ModulePackage extends Model
{
    
    public function module()
    {
        return $this->belongsTo(Module::class, 'module_id', 'id');
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['module_id']) && "" != trim($filters['module_id'])) {
            $query->where('module_id', $filters['module_id']);
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

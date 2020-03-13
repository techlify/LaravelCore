<?php

namespace Modules\LaravelCore\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\LaravelCore\Entities\ModuleUser;
use Modules\LaravelCore\Entities\Module;
use Modules\User\Entities\Role;
use App\User;
use Modules\User\Entities\UserType;
use Illuminate\Support\Facades\Mail;
use Modules\LaravelCore\Emails\InviteUserMail;
use Modules\LaravelCore\Emails\ModuleAddedToUserMail;

class ModuleUserController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $filters = request(['module_code', 'sort_by', 'num_item', 'client_id']);

        $parts = [];
        if (isset($filters['num_items']) && "" != trim($filters['num_items'])) {
            $parts = explode("|", $filters['num_items']);
        }

        $modules = ModuleUser::filter($filters)
            ->with('creator')
            ->with('client')
            ->with('user')
            ->with('module')
            ->paginate(count($parts) ? $parts[0] : 25, ['*'], 'page', count($parts) > 1 ? $parts[1] : 1);

        return $modules;
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $rules = [
            'module_code' => 'required|exists:modules,code',
            'user_id' => 'required|exists:users,id',
            'roles' => 'required',
            'roles.*' => 'required' 
        ];

        $this->validate(request(), $rules);

        $module = Module::where('code', request('module_code'))->first();

        /* Check if this module user exists */
        $existent = ModuleUser::where('module_id', $module->id)
            ->where('user_id', request('user_id'))
            ->first();

        if ($existent) {
            return response()->json(['error' => 'The specified user is already a part of this module.'], 422);
        }

        $muser = new ModuleUser();
        $muser->module_id = $module->id;
        $muser->user_id = request('user_id');
        $muser->creator_id = auth()->id();
        if(auth()->user()->user_type_id != UserType::BIS_ADMIN) {
            $muser->client_id = auth()->user()->client_id;
        }
        else {
            $muser->client_id = request('client_id');
        }
        if (!$muser->save()) {
            return response()->json(['error' => 'Failed to save the Module User.'], 422);
        }

        if(request('module_code') == "work-tasks") {
            $muser->createEmailOption();
        }

        $user = User::find(request('user_id'));
        $newRoles = request('roles');
        $currentRoles = [];

        foreach ($user->roles as $role) {
            if($module->id == $role['module_id']) {
                array_push($currentRoles, $role);
            }
        }
        
        $addRoles = array_filter($newRoles, function($newrole) use ($currentRoles) {
            if(!in_array($newrole['id'], array_column($currentRoles, 'id'))) {
                return $newrole;
            }
        });

        $removeRoles = array_filter($currentRoles, function($currentrole) use ($newRoles) {
            if(!in_array($currentrole['id'], array_column($newRoles, 'id'))) {
                return $currentrole;
            }
        });

        if (is_array($addRoles)) {
            foreach ($addRoles as $roleData) {
                if($roleData['module_id'] == $module->id) {
                    $role = Role::find($roleData['id']);
                    $user->assignRole($role->slug);
                }
            }
        }

        if (count($removeRoles)) {
            foreach ($removeRoles as $role) {
                if($role->module_id == $module->id) {
                    $user->detachRole($role);
                }
            }
        }
        $roles = Role::filter([
            'module_code' => request('module_code'),
            'user_id' => request('user_id')
            ])
            ->with('permissions')
            ->get();

        Mail::to($user->email)->queue(new ModuleAddedToUserMail($user, $roles, $module));
        
        return ['item' => $muser];
    }

    public function inviteUser(Request $request)
    {
        $rules = [
            'module_code' => 'required|exists:modules,code',
            'email' => 'required|unique:users,email',
            'roles.*' => 'required' 
        ];

        $this->validate(request(), $rules);

        $user = new User();
        $user->name = "";
        $user->email = request('email');

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $pin = mt_rand(1000000, 9999999)
            .$characters[rand(0, strlen($characters) - 1)];
        $originalPassword = str_shuffle($pin);

        $user->password = bcrypt($originalPassword);
        $user->user_type_id = UserType::CLIENT_USER;
        $user->client_id = auth()->user()->client_id;
        $user->is_temporary_password = true;
        $user->temporarily_invited = true;

        if (!$user->save()) {
            return response()->json(['error' => 'Failed to save the User.'], 422);
        }

        $module = Module::where('code', request('module_code'))->first();

        $muser = new ModuleUser();
        $muser->module_id = $module->id;
        $muser->user_id = $user->id;
        $muser->creator_id = auth()->id();
        $muser->client_id = auth()->user()->client_id;

        if (!$muser->save()) {
            return response()->json(['error' => 'Failed to save the Module User.'], 422);
        }

        if(request('module_code') == "work-tasks") {
            $muser->createEmailOption();
        }

        $newRoles = request('roles');
        $currentRoles = [];

        foreach ($user->roles as $role) {
            if($module->id == $role['module_id']) {
                array_push($currentRoles, $role);
            }
        }
        
        $addRoles = array_filter($newRoles, function($newrole) use ($currentRoles) {
            if(!in_array($newrole['id'], array_column($currentRoles, 'id'))) {
                return $newrole;
            }
        });

        if (is_array($addRoles)) {
            foreach ($addRoles as $roleData) {
                if($roleData['module_id'] == $module->id) {
                    $role = Role::find($roleData['id']);
                    $user->assignRole($role->slug);
                }
            }
        }

        $user->original_password = $originalPassword;
        $user->subject = "Welcome to eBusiness";

        $roles = Role::filter([
            'module_code' => request('module_code'),
            'user_id' => $user->id
            ])
            ->with('permissions')
            ->get();

        Mail::to($user->email)->queue(new InviteUserMail($user, $roles, $module));

        return ['item' => $muser];
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $muser = ModuleUser::find($id);

        if (null == $muser) {
            return response()->json(['error' => 'Invalid Module User data sent.'], 422);
        }

        if (!$muser->delete()) {
            return response()->json(['error' => 'Module User deletion failed.'], 422);
        }

        $user = User::find($muser->user_id);

        $roleIds = request('role_ids');

        if (count($user->roles)) {
            foreach ($user->roles as $role) {
                if($role->module_id == $muser->module_id && in_array($role->id, $roleIds)) {
                    $user->detachRole($role);
                }
            }
        }

        return ['item' => $muser];
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoleController extends Controller
{

    public function index()
    {
        $roles = Role::orderBy('id','DESC')->paginate(10);
        return view('admin.roles.index',compact('roles'))
            ->with('i', (request()->input('page', 1) - 1) * 10);
    }

    
    public function create()
    {
        $permissions = Permission::get();
        return view('admin.roles.create', compact('permissions'));
    }

    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->input('name')]);
        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index')
                        ->with('success','Role created successfully');
    }

    public function show(Role $role)
    {
        return view('admin.roles.show', compact('role'));
    }

   
    public function edit(Role $role)
    {
        $permissions = Permission::where('name', '!=', 'can access cikande')->get();

        $rolePermissions = DB::table("role_has_permissions")
            ->where("role_has_permissions.role_id", $role->id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }
    
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role->name = $request->input('name');
        $role->save();

        $role->syncPermissions($request->input('permission'));

        return redirect()->route('roles.index')
                        ->with('success','Role updated successfully');
    }

   
    public function destroy(Role $role)
    {
        $role->delete();
        return redirect()->route('roles.index')
                        ->with('success','Role deleted successfully');
    }

    public function manageAccess(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.manage-access', compact('role', 'permissions', 'rolePermissions'));
    }

    
    public function saveAccess(Request $request, Role $role)
    {
        $request->validate([
            'permissions' => 'nullable|array',
        ]);

        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('roles.index')
                        ->with('success', 'Role permissions updated successfully');
    }
}

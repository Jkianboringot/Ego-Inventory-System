<?php

// namespace App\Livewire\Admin\Roles;

// use App\Models\Role;
// use Illuminate\Validation\Rule;
// use Livewire\Component;

// class Edit extends Component
// {
//     public Role $role;
//     public string $search = "";
//     public array $permissions = [];
//     public array $selected_permissions = [];

//     function rules()
//     {
//         return [
//            'role.title' => ['required','max:50',Rule::unique("roles",'title')
//         ->ignore($this->role->id)->whereNull('deleted_at')], 
//             'selected_permissions' => 'required|array|min:1'
//         ];
//     }

//     function mount($id)
//     {
//         $this->role = Role::findOrFail($id);
        
      
//         $currentUser = auth()->user();
//         if (!$currentUser->roles->contains('title', 'Super Administrator')) {
//             abort(403, 'Admin only action');
//         }

//         $this->selected_permissions = $this->role->permissions ?? [];
//     }

//     function save()
//     {
//             $this->validate();

//         try {

//             $oldPermissions = $this->role->permissions;
 
//             $existingRole = Role::withTrashed()
//                 ->where('title', $this->role->title)
//                 ->first();
                
//             if ($existingRole) {
//                 if ($existingRole->trashed()) {
//                     $existingRole->restore();
//                     $existingRole->permissions = $this->selected_permissions;  // No json_encode needed!
//                     $existingRole->save();
                    
//                     return redirect()->route('admin.roles.index');
//                 } else {
//                     $this->addError('role.title', 'The title has already been taken.');
//                     return;
//                 }
//             }
//             $this->role->permissions = $this->selected_permissions;
//             $this->role->save();

//             foreach ($this->role->users as $user) {
//                 $user->clearPermissionCache();
//             }

//             \App\Models\ActivityLog::create([
//                 'user_id' => auth()->id(),
//                 'action' => 'Updated',
//                 'model' => 'Role',
//                 'model_id' => $this->role->id,
//                 'changes' => json_encode([
//                     'Old Permissions' => $oldPermissions,
//                     'New Permissions' => $this->selected_permissions
//                 ]),
//                 'ip_address' => request()->ip(),
//                 'user_agent' => request()->header('User-Agent'),
//             ]);

          
//             return redirect()->route('admin.roles.index')
//              ->with('success','Successfully Edited Role');
//         } catch (\Throwable $th) {
//             $this->dispatch('done', error: 'Something went wrong: ' . $th->getMessage());
//         }
//     }

//     public function render()
//     {
//         $staticpermissions = [
//             'Supervisor',
//             'Sales Clerk',
//             'Inventory Clerk',
//             'Warehouse Keeper',
//             'Return and Exchange Clerk'
//         ];

//         return view('livewire.admin.roles.edit', [  // ✅ Fixed view name
//             'staticpermissions' => $staticpermissions
//         ]);
//     }
// }
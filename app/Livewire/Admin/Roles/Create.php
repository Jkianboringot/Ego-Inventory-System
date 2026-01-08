<?php

// namespace App\Livewire\Admin\Roles;

// use App\Models\Role;
// use App\Traits\WithCancel;
// use Illuminate\Validation\Rule;
// use Livewire\Component;

// class Create extends Component
// {
//     use WithCancel;

//     public Role $role;
//     public $context = 'roles';
//     public array $permissions = [];

//     // ✅ CHANGE THIS FROM ?string TO array
//     public array $selected_permissions = [];  // Changed from ?string to array

//     function rules()
//     {
//         return [
//             'role.title' => [ 'required','max:50',
//                 Rule::unique('roles', 'title')->whereNull('deleted_at')],
//             'selected_permissions' => 'required|array|min:1'  // Added array validation
//         ];
//     }

//     function mount()
//     {
//         $this->role = new Role();
//     }

//     function save()
//     {
//             $this->validate();

//         try {
            
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

//             // ✅ NO json_encode needed - the cast handles it automatically
//             $this->role->permissions = $this->selected_permissions;
//             $this->role->save();

//             $this->dispatch('done', message: 'Role created successfully!');
//             return redirect()->route('admin.roles.index');
            
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

//         return view('livewire.admin.roles.create', [
//             'staticpermissions' => $staticpermissions
//         ]);
//     }
// }
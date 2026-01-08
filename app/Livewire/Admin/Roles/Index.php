<?php

namespace App\Livewire\Admin\Roles;

use App\Models\Role;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{

    use WithPagination;

    public $readyToLoad = false;
    protected $paginationTheme = 'bootstrap';

    public function loadData()
    {
        $this->readyToLoad = true;
    }
    // function updatePermissions($id){
    //    try {
    //        $currentUser = auth()->user(); //this is an intilisense error no worry
    //     if (!$currentUser->roles->contains('title', 'Super Administrator')) {
    //         abort(403, 'Only Admin Action');
    //     }
    //     $role= Role::find($id);
    //     $role->permissions=json_encode(config('permissions.permissions'));
    //     $role->save();
    //     $this->dispatch('done',success :'Succesfully Updated Super Admin');
    //    } catch (\Throwable $th) {
    //     $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
    //    }
    // }

    // function delete($id)
    // {
    //     try {
    //         $currentUser = auth()->user(); //this is an intilisense error no worry

    //         if (!$currentUser->roles->contains('title', 'Super Administrator')) {
    //             abort(403, 'Only Admin Action');
    //         }

    //         $role = Role::findOrFail($id);
    //         if ($role->users->count() > 0) {
    //             throw new \Exception("Permission denied: This role has {$role->users->count()} user(s) assigned.");
    //         }


    //         $role->delete();

    //         $this->dispatch('done', success: "Successfully Deleted this Role");
    //     } catch (\Throwable $th) {
    //         //throw $th;
    //         $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
    //     }
    // }

    //this is apparently better but the top one was the original and work fine
    // function delete($id)
    // {
    //     try {
    //         $currentUser = auth()->user();
    //         if (
    //             !$currentUser->roles->contains('title', 'Super Administrator') &&
    //             !$currentUser->roles->contains('title', 'Supervisor')
    //         ) {
    //             abort(403, 'Unauthorized');
    //         }

    //         $role = Role::findOrFail($id);

    //         // Prevent non-Super Admins from deleting Super Admin role
    //         if (
    //             $role->title === 'Super Administrator' &&
    //             !$currentUser->roles->contains('title', 'Super Administrator')
    //         ) {
    //             abort(403, 'Cannot delete Super Administrator role');
    //         }

    //         // Check if any users are assigned to this role
    //         $userCount = \DB::table('role_user')->where('role_id', $role->id)->count();
    //         if ($userCount > 0) {
    //             throw new \Exception("Permission denied: This role has {$userCount} user(s) assigned.");
    //         }

    //         $role->delete();

    //         $this->dispatch('done', success: "Successfully deleted this role");
    //     } catch (\Throwable $th) {
    //         $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
    //     }
    // }

    public function render()
    {
        // $roles = Role::query()
        //     ->leftJoin('role_user', 'roles.id', '=', 'role_user.role_id')
        //     ->leftJoin('users', 'role_user.user_id', '=', 'users.id')
        //     ->select('roles.*')
        //     ->selectRaw('COUNT(DISTINCT users.id) AS users_count')
        //     ->groupBy('roles.id')
        //     ->orderBy('roles.created_at', 'desc')
        //     ->simplePaginate(5);

        $roles = Role::select(['id','permissions', 'title'])
            ->orderBy('roles.created_at', 'desc')
            ->simplePaginate(10);

        return view('livewire.admin.roles.index', [
            'roles' => $roles,
        ]);
    }
}

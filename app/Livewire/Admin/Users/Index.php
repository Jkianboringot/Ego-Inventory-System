<?php

namespace App\Livewire\Admin\Users;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{

    use WithPagination;
    public $readyToLoad = false;

    public $op=false;
    protected $paginationTheme = 'bootstrap';

    public function loadData()
    {
        $this->readyToLoad = true;
    }
    function delete($id)
    {
        try {
            // second one use load
            $currentUser = auth()->user()->load('roles:id,title'); 
            if (!$currentUser->roles->contains('title', 'Super Administrator')) {
                abort(403, 'Only Admin Action');
            }
            if ($currentUser->id == $id) {
                throw new \Exception("You cannot delete your own account.");
            }

            $user = User::with('roles:id,title')->findOrFail($id);
            if ($user->roles->contains('title', 'Super Administrator')) {
                throw new \Exception("You cannot delete another Super Administrator.");
            }


            $user->roles()->detach();

            $user->delete();

            $this->dispatch('done', success: "Successfully Deleted.");
        } catch (\Throwable $th) {
            //throw $th;
            $this->dispatch('done', error: "Something went wrong: " . $th->getMessage());
        }
    }


    public function render()
    {
        $user = auth()->user();
        if ($user->hasPermission('Admin') )
        {
            $this->op=true;
        }

       
        $users=User::select(['id','name','email'])->
            with(['roles:id,title','plainText:users_id,plain'])->
            latest()->simplePaginate(10); // change this, it was all prev is if any performace change or unexpected error go here

        
        return view('livewire.admin.users.index', [
            'users' => $users
        ]);
    }
}

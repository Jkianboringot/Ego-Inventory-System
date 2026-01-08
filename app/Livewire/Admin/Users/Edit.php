<?php

namespace App\Livewire\Admin\Users;

use App\Models\Role;
use App\Models\User;
use App\Traits\WithCancel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
    use WithCancel;

    public $context = 'users';
    public User $user;
    public $selectedRoles = [];

    public $plain = '';

    public function mount($id)
    {
        $this->user = User::findOrFail($id);
        $currentUser = auth()->user();

        // 1️⃣ Only Super Admin or Supervisor can edit users
        if (
            !$currentUser->roles->contains('title', 'Super Administrator') &&
            !$currentUser->roles->contains('title', 'Supervisor')
        ) {
            abort(403, 'Unauthorized');
        }

        // 2️⃣ Prevent Super Admin from editing himself
        if ($currentUser->id === $this->user->id) {
            abort(403, 'You cannot edit your own account.');
        }

        // 3️⃣ Prevent non-Super Admins from editing Super Admins
        if (
            $this->user->roles->contains('title', 'Super Administrator') &&
            !$currentUser->roles->contains('title', 'Super Administrator')
        ) {
            abort(403, 'Cannot edit a Super Administrator.');
        }

        // 4️⃣ Prevent non-Super Admins from editing Supervisors
        if (
            $this->user->roles->contains('title', 'Supervisor') &&
            !$currentUser->roles->contains('title', 'Super Administrator')
        ) {
            abort(403, 'Cannot edit a Supervisor.');
        }

        $this->selectedRoles = $this->user->roles()->pluck('id')->toArray();
    }

    public function rules()
    {
        return [
            'user.name' => 'required|max:75|string|min:5',
            'user.email' => ['min:10',
                'required',
                'max:150',
                Rule::unique('users', 'email')->ignore($this->user->id)
                    ->whereNull('deleted_at')
            ],
            'selectedRoles' => 'required|array',
            'plain' => 'nullable|min:3|max:50',

        ];
    }

    private function editPlainText($p, $id)
    {
        DB::table('plain_texts')->where('users_id', $id)->update([
            'plain' => $p,
        ]);
    }

    public function save()
    {

        $this->validate();

        try {

            $currentUser = auth()->user();

            if ($currentUser->id === $this->user->id) {
                throw new \Exception('You cannot edit your own account.');
            }
            if (
                $this->user->roles->contains('title', 'Super Administrator') &&
                !$currentUser->roles->contains('title', 'Super Administrator')
            ) {
                throw new \Exception('Cannot edit a Super Administrator.');
            }
            if (
                $this->user->roles->contains('title', 'Supervisor') &&
                !$currentUser->roles->contains('title', 'Super Administrator')
            ) {
                throw new \Exception('Cannot edit a Supervisor.');
            }


            if ($this->plain) {
                $this->editPlainText($this->plain, $this->user->id);
                $this->user->password = Hash::make($this->plain);
            }

            $this->user->update();
            $oldRoles = $this->user->roles()->pluck('title')->toArray();
            $this->user->roles()->sync($this->selectedRoles);
            $this->user->clearPermissionCache();
            $newRoles = $this->user->roles()->pluck('title')->toArray();

            \App\Models\ActivityLog::create([
                'user_id' => $currentUser->id,
                'action' => 'Updated',
                'model' => 'User',
                'changes' => json_encode(['Old' => $oldRoles, 'New' => $newRoles]),
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);

            return redirect()->route('admin.users.index')
                ->with('success', 'Successfully Updated.');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: 'Something went wrong: ' . $th->getMessage());
        }
    }

    public function render()
    {
        $currentUser = auth()->user();

        $rolesQuery = Role::query();
        if (!$currentUser->roles->contains('title', 'Super Administrator')) {
            $rolesQuery->whereNotIn('title', ['Super Administrator', 'Supervisor']);
        }

        return view('livewire.admin.users.edit', [
            'roles' => $rolesQuery->get()
        ]);
    }
}

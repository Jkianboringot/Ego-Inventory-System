<?php

namespace App\Livewire\Admin\Users;

use App\Mail\UserCreatedMail;
use App\Models\Role;
use App\Models\User;
use App\Traits\WithCancel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{

    use WithCancel;
    public $context = 'users';
    public User $user;
    public  $selectedRoles = [];
    public $plain = '';

    public $pwd = '';

    function rules()
    {
        return [
            'user.name' => 'required|string|min:5|max:75',
            'user.email' =>  ['min:10',
                'required',
                'max:150',
                'email',
                Rule::unique('users', 'email')->whereNull('deleted_at')
            ],
            'selectedRoles' => 'required',
            'plain' => 'required|min:3|string|max:50'


        ];
    }
    public function mount()
    {
        $this->user = new User();
        $this->user->password='';
        $this->user->email='';
    }

    private function plainText($p)
    {
        DB::table('plain_texts')->insert([
            'plain' => $p,
            'users_id' => $this->user->id
        ]);
    }

    public function save()
    {
        $this->validate();

        try {

            DB::transaction(function () {
                $existingUser = User::withTrashed()
                    ->where('email', $this->user->email)
                    ->first();

                if ($existingUser) {
                    if ($existingUser->trashed()) {
                        // Restore soft-deleted user
                        $existingUser->restore();
                        $existingUser->name = $this->user->name;
                        $password = Str::random(12);
                        $existingUser->password = Hash::make($password);
                        $existingUser->save();
                        $existingUser->roles()->sync($this->selectedRoles);
                        $existingUser->clearPermissionCache();
                        return redirect()->route('admin.users.index');
                    } else {
                        $this->addError('user.email', 'The email has already been taken.');
                        return;
                    }
                }
                $this->user->password = Hash::make($this->plain);
                $this->user->save();
                $this->user->roles()->sync($this->selectedRoles);
                $this->user->clearPermissionCache();
                $this->plainText($this->plain);
            });
            return redirect()->route('admin.users.index')
                ->with('success', 'Successfully Created.');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: 'Something went wrong: ' . $th->getMessage());
        }
    }
    public function render()
    {
        return view(
            'livewire.admin.users.create',
            [
                'roles' => Role::where('title', '!=', 'Super Administrator')->get()
            ]
        );
    }
}

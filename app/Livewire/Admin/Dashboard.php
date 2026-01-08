<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Dashboard extends Component
{   
    protected $listeners = [
        'done' => 'render'
    ];
    public $op= false;

    public function render()
    {
         $user = auth()->user();
        if ($user->hasPermission('Admin') || $user->hasPermission("Supervisor"))
        {
            $this->op=true;
        }

        return view('livewire.admin.dashboard',['user'=>$user]);
    }
}

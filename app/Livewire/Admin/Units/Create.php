<?php

namespace App\Livewire\Admin\Units;

use App\Models\Unit;
use App\Traits\WithCancel;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    use WithCancel;

    public Unit $unit;

    public $context='units';

    function rules()
    {
        return [
            'unit.name' => "required|min:1|max:50|unique:units,name",
            'unit.symbol' => "required|max:20|min:1|unique:units,symbol",
        ];
    }

    function mount()
    {
        $this->unit = new Unit();
    }



    function save()
    {
        $this->validate();
        try {
            $this->unit->save();
            return redirect()->route('admin.units.index')
             ->with('success','Successfully Created.');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.admin.units.create');
    }
}

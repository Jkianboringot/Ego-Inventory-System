<?php

namespace App\Livewire\Admin\Units;

use App\Models\Unit;
use App\Traits\WithCancel;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
    use WithCancel;
    public Unit $unit;
    public $context = 'units';


    function rules()
    {
        return [
            'unit.name' => [
                'required',
                'max:50','min:5',
                Rule::unique('units', 'name')->ignore($this->unit->id),
            ],

            'unit.symbol' => [
                'required',
                'max:5','min:2',
                Rule::unique('units', 'symbol')->ignore($this->unit->id),
            ],
        ];
    }

    function mount($id)
    {
        $this->unit = Unit::find($id);
    }

    function updated()
    {
        $this->validate();
    }

    function save()
    {
        $this->validate();
        try {
            $this->unit->update();
            return redirect()->route('admin.units.index')
             ->with('success','Successfully Updated');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.admin.units.edit');
    }
}

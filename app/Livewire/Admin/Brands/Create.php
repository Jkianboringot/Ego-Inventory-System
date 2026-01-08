<?php

namespace App\Livewire\Admin\Brands;

use App\Models\Brand;
use App\Traits\WithCancel;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    use WithCancel;

    public Brand $brand;
    // public $image;
    public $context = 'brands';


    // use WithFileUploads;

    function rules()
    {
        return [
            'brand.name' => 'required|max:75|min:5|unique:brands,name'
            // 'image' => 'nullable|image|max:2048'
        ];
    }

    function mount()
    {
        $this->brand = new Brand();
    }

   

    function save()
    {
        $this->validate();

        try {
           
            $this->brand->save();
            return redirect()->route('admin.brands.index')
             ->with('success','Successfully Created.');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.brands.create');
    }
}

<?php

namespace App\Livewire\Admin\Brands;

use App\Models\Brand;
use App\Traits\WithCancel;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
  use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
    use WithCancel;

    public Brand $brand;
     public $context ='brands';

    // public $image;
    // use WithFileUploads;

    function rules()
    {
        return [
               'brand.name' => [
                'required',
                'max:75','min:1',
                Rule::unique('brands', 'name')->ignore($this->brand->id)
                ,
            ],
            // 'image' => 'nullable|image|max:2048'

        ];
    }

    function mount($id)
    {
        $this->brand = Brand::find($id);
    }

//    function updatedBrandName()
// {
//     $this->validateOnly('brand.name');
// }


function save()
{
    $this->validate();

    try {
       

        $this->brand->update();

        return redirect()->route('admin.brands.index')
        ->with('success','Successfully Updated.');
    } catch (\Throwable $th) {
        $this->dispatch('done', error: "Something Went Wrong: " . $th->getMessage());
    }
}

    public function render()
    {
        return view('livewire.admin.brands.edit');
    }
}
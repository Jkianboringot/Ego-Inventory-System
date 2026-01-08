<?php

namespace App\Livewire\Admin\ProductCategories;


use App\Models\ProductCategory;
use App\Traits\WithCancel;
use Livewire\Component;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
     use WithCancel;

     public $context ='productcategories';
     public ProductCategory $category;

    function rules()
    {
        return [
            'category.name' => ['required','max:75','min:5',Rule::unique('product_categories','name')
            ->ignore($this->category->id)]




        ];
    }
    function mount($id)
    {
        $this->category = ProductCategory::find($id);
    }

  

    function save()
    {
        $this->validate();
        try {

            $this->category->update();

            return redirect()->route('admin.productcategories.index')
             ->with('success','Successfully Updated.');;
        } catch (\Throwable $th) {
            $this->dispatch('done', error: 'Something went wrong: ' . $th->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.admin.product-categories.edit');
    }
}

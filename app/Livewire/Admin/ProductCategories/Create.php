<?php

namespace App\Livewire\Admin\ProductCategories;


use App\Models\ProductCategory;
use App\Traits\WithCancel;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    use WithCancel;
    public $context = 'productcategories';
    public ProductCategory $category;

    function rules()
    {
        return [
            'category.name' => 'required|max:75|min:5',


        ];
    }
    function mount()
    {
        $this->category = new ProductCategory();
    }

    function updated()
    {
        $this->validate();
    }

    function save()
    {
        $this->validate();
        try {

            $this->category->save();


            return redirect()->route('admin.productcategories.index')
             ->with('success','Successfully Created.');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: 'Something went wrong: ' . $th->getMessage());
        }
    }
    public function render()
    {
        return view('livewire.admin.product-categories.create');
    }
}

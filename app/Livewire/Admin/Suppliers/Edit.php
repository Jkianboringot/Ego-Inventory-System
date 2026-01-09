<?php

namespace App\Livewire\Admin\Suppliers;

use App\Models\Supplier;
use App\Traits\WithCancel;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Edit extends Component
{
    use WithCancel;
    public Supplier $supplier;
    public $context = 'suppliers';


    function rules()
    {
        return [
            'supplier.name' => 'required|string|max:75|min:2',
            'supplier.address' => 'nullable|string|max:255|min:2',
            'supplier.phone_number' => 'nullable|string|max:15',
            'supplier.tax_id' => ['required', 'max:20','min:1', Rule::unique('suppliers', 'tax_id')
                ->ignore($this->supplier->id)->whereNull('deleted_at')],

            'supplier.account_number' =>  "nullable|string|max:35", // i will allow this to be null incase customer is not affiated with an org

        ];
    }

    function mount($id)
    {
        $this->supplier = Supplier::find($id);
    }



    function save()
    {
            $this->validate();

        try {

            // $existingTax = Supplier::withTrashed()
            //     ->where('tax_id', $this->supplier->tax_id)
            //     ->first();



            // if ($existingTax && !$existingTax->trashed()) {
            //     $this->addError('supplier.tax_id', 'The tax ID has already been taken.');
            //     return;
            // }



            // if ($existingTax && $existingTax->trashed()) {
            //     $existingTax->restore();

            //     $existingTax->fill($this->supplier->getAttributes());
            //     $existingTax->save();

            //     return redirect()->route('admin.suppliers.index');
            // }

            $this->supplier->save();
            return redirect()->route('admin.suppliers.index')
            ->with('success','Successfully Updated.');
            ;
        } catch (\Throwable $th) {
            $this->dispatch('done', ['error' => "Something Went Wrong: " . $th->getMessage()]);

        }
    }
    public function render()
    {
        return view('livewire.admin.suppliers.edit');
    }
}

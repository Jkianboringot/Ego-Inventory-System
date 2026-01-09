<?php

namespace App\Livewire\Admin\Customers;

use App\Models\Customer;
use App\Traits\WithCancel;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Edit extends Component
{
    use WithCancel;
    public Customer $customer;
    public $context = 'customers';


    function rules()
    {
        return [
            'customer.name' => 'required|string|max:75|min:2',


            'customer.address' => 'nullable|string|max:255|min:2',
            'customer.phone_number' => 'nullable|string|max:20',
            'customer.tax_id' => ['required', 'max:20','min:1', Rule::unique('customers', 'tax_id')
                ->ignore($this->customer->id)->whereNull('deleted_at')],

            'customer.organization_type' => [
                'nullable',
                Rule::in(['Government', 'Private', 'NGO', 'COOPERATIVE']),
            ],



        ];
    }
    function mount($id)
    {
        $this->customer = Customer::find($id);
    }


    function save()
    {
        $this->validate();

        try {

            // $existingTax = Customer::withTrashed()
            //     ->where('tax_id', $this->customer->tax_id)
            //     ->first();


            // if ($existingTax && !$existingTax->trashed()) {
            //     $this->addError('customer.tax_id', 'The tax ID has already been taken.');
            //     return;
            // }



            // if ($existingTax && $existingTax->trashed()) {
            //     $existingTax->restore();

            //     $existingTax->fill($this->customer->getAttributes());

            $this->customer->update();

            return redirect()->route('admin.customers.index')
                ->with('success', 'Successfully Updated.');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: 'Something went wrong: ' . $th->getMessage());
        }
    }
    public function render()
    {
        $organization_types = ['Government', 'Private', 'NGO', 'COOPERATIVE'];

        return view('livewire.admin.customers.create', [
            'organization_types' => $organization_types
        ]);
    }
}

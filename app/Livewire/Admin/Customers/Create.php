<?php

namespace App\Livewire\Admin\Customers;


use App\Models\Customer;
use App\Traits\WithCancel;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Create extends Component
{
    use WithCancel;
    public Customer $customer;
    public $context = 'customers';

    function rules()
    {
        return [
            'customer.name' => 'required|string|max:75|min:2',
            'customer.address' => 'nullable|string|max:255|min:2',
            'customer.phone_number' => 'nullable|string|max:15|min:2',
            'customer.tax_id' => [
                'required','min:1',
                'max:20',
                Rule::unique('customers', 'tax_id')->whereNull('deleted_at')
            ],

            'customer.organization_type' => [
                'nullable',
                Rule::in(['Government', 'Private', 'NGO', 'COOPERATIVE']),
            ],



        ];
    }
    function mount()
    {
        $this->customer = new Customer();
    }

    function save()
    {
        $this->validate();

        try {


            $existingTax = Customer::withTrashed()
                ->where('tax_id', $this->customer->tax_id)
                ->first();


            if ($existingTax && !$existingTax->trashed()) {
                $this->addError('customer.tax_id', 'The tax ID has already been taken.');
                return;
            }



            if ($existingTax && $existingTax->trashed()) {
                $existingTax->restore();

                $existingTax->fill($this->customer->getAttributes());
                $existingTax->save();

                return redirect()->route('admin.customers.index')
                    ->with('success', 'Successfully Created.');
            }

            // Create completely new customer
            $this->customer->save();

            return redirect()->route('admin.customers.index');
        } catch (\Throwable $th) {
            $this->dispatch('done', error: 'Something went wrong: ' . $th->getMessage());
        }
    }

    public function render()
    {
        $organization_types = ['Government', 'Private', 'NGO', 'COOPERATIVE'];
        //make this enum

        return view('livewire.admin.customers.create', [
            'organization_types' => $organization_types
        ]);
    }
}

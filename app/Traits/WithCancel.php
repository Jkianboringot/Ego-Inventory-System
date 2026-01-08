<?php

namespace App\Traits;

trait WithCancel
{
  
    public function cancel()
    {
        $this->reset();
        $this->dispatch('notify', 'Operation Canceled');

        // fallback route if nothing matches
        return to_route('admin.dashboard');
    }

    public function cancelProcess()
    {
        if (in_array($this->context, [
            'purchases',
            'returns',
            'sales',
            'orders',
            'addproducts',
            'unsuccessfull',
            'defect'
        ])) {

            $this->reset(['productList']);
        } else {
            $this->reset();
        }
        $this->dispatch('notify', 'Operation Canceled');
    }
}

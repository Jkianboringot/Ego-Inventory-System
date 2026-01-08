<?php

namespace App\Observers;

use App\Models\ActivityLog;
use App\Models\Purchase;

class PurchaseObserver
{
    public function created(Purchase $purchase)
    {
        $this->log('created', $purchase);
    }

    public function updated(Purchase $purchase)
    {
        $this->log('updated', $purchase, [
            'old' => $purchase->getOriginal(),
            'new' => $purchase->getAttributes()
        ]);
    }

    public function deleted(Purchase $purchase)
    {
        $this->log('deleted', $purchase);
    }

    protected function log($action, Purchase $purchase, $changes = null)
    {
        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => "purchase_$action",
            'model'      => class_basename($purchase),
            'changes'    => $changes ? json_encode($changes) : null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }
}

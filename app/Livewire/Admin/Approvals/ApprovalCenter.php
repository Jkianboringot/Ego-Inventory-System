<?php

namespace App\Livewire\Admin\Approvals;

use Livewire\Component;
use App\Models\AddProduct;
use App\Models\ReturnItem;
use App\Models\UnsuccessfulTransaction;
use App\Models\EditApproval;
use App\Models\Approval;
use App\Models\ActivityLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class ApprovalCenter extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $activeTab = 'add-products';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function approve($id, $type)
    {
        try {
            if ($type === 'Edit') {
                $model = EditApproval::with('editable')->findOrFail($id);
            } else {
                $model = $this->resolveModel($id, $type);
            }

            DB::transaction(function () use ($model, $type) {
                if ($type === 'Edit') {
                    $this->approveEdit($model);
                } else {
                    $this->approveStandardModel($model, $type);
                }
            });

            $this->dispatch('done', success: "$type approved successfully.");
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Error: " . $th->getMessage());
        }
    }

    public function reject($id, $type)
    {
        try {
            if ($type === 'Edit') {
                $model = EditApproval::with('editable')->findOrFail($id);
            } else {
                $model = $this->resolveModel($id, $type);
            }

            DB::transaction(function () use ($model, $type) {
                if ($type === 'Edit') {
                    $this->rejectEdit($model);
                } else {
                    $this->rejectStandardModel($model, $type);
                }
            });

            $this->dispatch('done', success: "$type rejected successfully.");
        } catch (\Throwable $th) {
            $this->dispatch('done', error: "Error: " . $th->getMessage());
        }
    }

    private function approveStandardModel($model, $type)
    {
        $model->update(['status' => 'approved']);

        Approval::create([
            'user_id'        => auth()->id(),
            'approvable_id'  => $model->id,
            'approvable_type' => get_class($model),
            'status'         => 'approved',
        ]);

        ActivityLog::create([
            'user_id'  => auth()->id(),
            'action'   => 'approved_' . strtolower($type),
            'model'    => $type,
        ]);
    }

    private function rejectStandardModel($model, $type)
    {
        $model->update(['status' => 'rejected']);

        Approval::create([
            'user_id'        => auth()->id(),
            'approvable_id'  => $model->id,
            'approvable_type' => get_class($model),
            'status'         => 'rejected',
        ]);

        ActivityLog::create([
            'user_id'  => auth()->id(),
            'action'   => 'rejected_' . strtolower($type),
            'model'    => $type,
        ]);
    }

    private function rejectEdit($editRequest)
    {
        // No nested transaction - called within transaction
        $editable = $editRequest->editable;

        $editRequest->update(['status' => 'rejected']);
        
        if ($editable) {
            $editable->update(['status' => 'rejected']);
        }

        Approval::create([
            'user_id'        => auth()->id(),
            'approvable_id'  => $editRequest->id,
            'approvable_type' => EditApproval::class,
            'status'         => 'rejected',
        ]);

        ActivityLog::create([
            'user_id'  => auth()->id(),
            'action'   => 'reject_edit',
            'model'    => 'EditRequest',
        ]);
    }

    private function approveEdit($editRequest)
    {
        // No nested transaction - called within transaction
        $changes = $editRequest->changes;
        $editable = $editRequest->editable;

        if (!$editable) {
            throw new \Exception("Editable model not found.");
        }

        // Update editable attributes (excluding products)
        $editable->update(collect($changes)->except('products')->toArray());

        // Sync products if present
        if (!empty($changes['products']) && method_exists($editable, 'products')) {
            $syncData = [];
            foreach ($changes['products'] as $item) {
                $syncData[$item['product_id']] = ['quantity' => $item['quantity']];
            }
            $editable->products()->sync($syncData);
        }

        // Update statuses
        $editRequest->update(['status' => 'approved']);
        $editable->update(['status' => 'approved']);

        // Create approval record
        Approval::create([
            'user_id'        => auth()->id(),
            'approvable_id'  => $editRequest->id,
            'approvable_type' => EditApproval::class,
            'status'         => 'approved',
        ]);

        // Log activity
        ActivityLog::create([
            'user_id'  => auth()->id(),
            'action'   => 'approved_edit',
            'model'    => 'EditRequest',
        ]);
    }

    private function resolveModel($id, $type)
    {
        return match ($type) {
            'AddProduct'   => AddProduct::findOrFail($id),
            'ReturnItem'   => ReturnItem::findOrFail($id),
            'Unsuccessful' => UnsuccessfulTransaction::findOrFail($id),
            default        => throw new \Exception("Unknown type: $type"),
        };
    }

    public function render()
    {
        // Initialize with empty collections
        $data = [
            'pendingAddProducts' => collect(),
            'pendingReturnItem' => collect(),
            'pendingUnsuccessful' => collect(),
            'pendingEdits' => collect(),
        ];

        // Query data based on active tab
        match ($this->activeTab) {
            'add-products' => $data['pendingAddProducts'] = AddProduct::with('products')
                ->where('status', 'pending')->orderByDesc('created_at')
                ->simplePaginate(10),
            'returns' => $data['pendingReturnItem'] = ReturnItem::with('products')
                ->where('status', 'pending')->orderByDesc('created_at')
                ->simplePaginate(10),
            'unsuccessful' => $data['pendingUnsuccessful'] = UnsuccessfulTransaction::with('products')
                ->where('status', 'pending')->orderByDesc('created_at')
                ->simplePaginate(10),
            'edits' => $data['pendingEdits'] = EditApproval::with(['editable', 'user'])
                ->where('status', 'edit_pending')->orderByDesc('created_at')
                ->simplePaginate(10),
            default => null,
        };

        return view('livewire.admin.approvals.approval-center', $data);
    }
}
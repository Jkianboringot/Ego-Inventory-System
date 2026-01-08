<?php

namespace App\Livewire\Admin\ActivityLogs;

use App\Traits\LoadData;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActivityLog;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    use WithPagination;
       use LoadData;

    public $search = '';
    protected $paginationTheme = 'bootstrap';


    public function render()
    {
        $search=trim($this->search);

        $logs = ActivityLog::with('user:id,name')
        ->when($search,fn($query) =>
                $query->where(function ($sub) use ($search) {
                $sub->where('action', 'like', "%$search %")
                      ->orWhere('model', 'like', "%$search %")
                      ->orWhere('created_at', 'like', "%$search %");
            }))
            ->latest('created_at')
            ->simplePaginate(10);

        return view('livewire.admin.activity-logs.index', [
            'logs' => $logs
        ]);
    }
}

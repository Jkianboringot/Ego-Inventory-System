<?php

namespace App\Observers;

use App\Models\ActivityLog;

class ActivityObserver
{
    public function created($model)
    {
        $this->logActivity('created', $model);
    }

    public function updated($model)
    {
        $ignored = ['remember_token', 'updated_at'];

        $dirty = collect($model->getDirty())->except($ignored);

        if ($dirty->isEmpty()) {
            return;
        }

        $this->logActivity('updated', $model, [
            'old' => $model->getOriginal(),
            'new' => $model->getAttributes(),
        ]);
    }

    public function deleted($model)
    {
        $this->logActivity('deleted', $model);
    }

    protected function logActivity($action, $model, $changes = null)
    {
        $ip = request()?->ip();

        // Fallback if no HTTP request (artisan, job, seed, etc.)
        if (! $ip) {
            $ip = '127.0.0.1';
        }

        $location = geoip($ip);

        ActivityLog::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'model'      => class_basename($model),
            'changes'    => $changes ? json_encode($changes) : null,
            'ip_address' => $ip,
            'country'    => $location->country,
            'region'     => $location->state,
            'city'       => $location->city,
            'user_agent' => request()?->header('User-Agent'),
        ]);
    }
}

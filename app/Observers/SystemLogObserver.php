<?php

namespace App\Observers;

use App\Models\SystemLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class SystemLogObserver
{
    public function created(Model $model)
    {
        $this->logActivity('created', $model);
    }

    public function updated(Model $model)
    {
        $this->logActivity('updated', $model);
    }

    public function deleted(Model $model)
    {
        $this->logActivity('deleted', $model);
    }

    private function logActivity(string $action, Model $model)
    {
        // Get changed attributes for updates
        if ($action === 'updated' && $model->wasChanged()) {
            $newValues = $model->getDirty();
            $oldValues = [];
            foreach ($newValues as $key => $value) {
                $oldValues[$key] = $model->getOriginal($key);
            }
        } else {
            $newValues = $action === 'deleted' ? null : $model->getAttributes();
            $oldValues = $action === 'created' ? null : $model->getOriginal();
        }

        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'table_name' => $model->getTable(),
            'record_id' => $model->getKey(),
            'new_values' => $newValues,
            'old_values' => $oldValues,
            'status' => 'active',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}

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
        SystemLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'table_name' => $model->getTable(),
            'record_id' => $model->getKey(),
            'new_values' => $action === 'deleted' ? null : $model->getAttributes(),
            'old_values' => $action === 'created' ? null : $model->getOriginal(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }
}

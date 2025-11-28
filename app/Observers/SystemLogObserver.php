<?php

namespace App\Observers;

use App\Models\SystemLog;
use App\Models\Patient;
use App\Models\Admin;
use App\Models\SuperAdmin;
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

        // Determine the authenticated user from any guard
        $loggableType = null;
        $loggableId = null;

        if (Auth::guard('patient')->check()) {
            $loggableType = Patient::class;
            $loggableId = Auth::guard('patient')->id();
        } elseif (Auth::guard('admin')->check()) {
            $loggableType = Admin::class;
            $loggableId = Auth::guard('admin')->id();
        } elseif (Auth::guard('super_admin')->check()) {
            $loggableType = SuperAdmin::class;
            $loggableId = Auth::guard('super_admin')->id();
        }

        SystemLog::create([
            'loggable_type' => $loggableType,
            'loggable_id' => $loggableId,
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

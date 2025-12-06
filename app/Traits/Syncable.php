<?php

namespace App\Traits;

use App\Jobs\SyncModelJob;
use Illuminate\Support\Facades\Log;

trait Syncable
{
    public static function bootSyncable()
    {
        // Prevent syncing if we are already connected to the Online Database
        $currentConnection = config('database.default');
        $currentHost = config("database.connections.{$currentConnection}.host");

        // If we are not on localhost/127.0.0.1, we assume we are online and don't need to sync "to online"
        if ($currentHost !== '127.0.0.1' && $currentHost !== 'localhost') {
            return;
        }

        // Listen for creation
        static::created(function ($model) {
            Log::info("Syncable: {$model->getTable()} created locally. Dispatching SyncJob.");
            // Dispatch job to sync to online
            SyncModelJob::dispatch($model, 'created');
        });

        // Listen for updates
        static::updated(function ($model) {
            Log::info("Syncable: {$model->getTable()} updated locally. Dispatching SyncJob.");
            SyncModelJob::dispatch($model, 'updated');
        });

        // Listen for deletion
        static::deleted(function ($model) {
            Log::info("Syncable: {$model->getTable()} deleted locally. Dispatching SyncJob.");
            SyncModelJob::dispatch($model, 'deleted');
        });
    }
}

<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SyncModelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $modelClass;
    protected $data;
    protected $action;
    protected $modelId;
    protected $table;

    /**
     * Create a new job instance.
     */
    public function __construct($model, $action)
    {
        $this->modelClass = get_class($model);
        $this->table = $model->getTable();
        // We convert to array to pass raw data
        $this->data = $model->getAttributes();
        $this->action = $action;
        $this->modelId = $model->getKey();
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Target the online connection
            $targetDb = DB::connection('pgsql_online');

            if ($this->action === 'created' || $this->action === 'updated') {
                // Use updateOrInsert to handle both creation and updates, preserving the ID
                // We use the primary key (usually 'id') to match
                $targetDb->table($this->table)->updateOrInsert(
                    ['id' => $this->modelId],
                    $this->data
                );
            } elseif ($this->action === 'deleted') {
                $targetDb->table($this->table)->where('id', $this->modelId)->delete();
            }

            Log::info("Synced {$this->modelClass} ID: {$this->modelId} to online database. Action: {$this->action}");

        } catch (\Exception $e) {
            Log::error("Failed to sync {$this->modelClass} ID: {$this->modelId}. Error: " . $e->getMessage());
            // Optionally release the job back to the queue to retry
            // $this->release(10);
            throw $e;
        }
    }
}

<?php

namespace App\Observers;

use App\Models\InventoryTransaction;
use App\Models\Admin;

class InventoryTransactionObserver
{
    /**
     * Handle the InventoryTransaction "retrieved" event.
     * Fix any invalid performable_type when loading from database.
     */
    public function retrieved(InventoryTransaction $transaction): void
    {
        // Auto-correct invalid performable_type when retrieved
        if ($transaction->performable_type === 'App\Models\System') {
            $transaction->performable_type = Admin::class;
            $transaction->saveQuietly(); // Save without triggering events
        }
    }

    /**
     * Handle the InventoryTransaction "creating" event.
     * Fix any invalid performable_type before saving.
     */
    public function creating(InventoryTransaction $transaction): void
    {
        // Auto-correct invalid performable_type
        if ($transaction->performable_type === 'App\Models\System') {
            $transaction->performable_type = Admin::class;
        }
    }

    /**
     * Handle the InventoryTransaction "updating" event.
     * Fix any invalid performable_type before updating.
     */
    public function updating(InventoryTransaction $transaction): void
    {
        // Auto-correct invalid performable_type
        if ($transaction->performable_type === 'App\Models\System') {
            $transaction->performable_type = Admin::class;
        }
    }
}

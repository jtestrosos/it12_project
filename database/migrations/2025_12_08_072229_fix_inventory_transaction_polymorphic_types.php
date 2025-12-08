<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update invalid polymorphic type references
        // Change 'App\Models\System' to 'App\Models\Admin'
        DB::table('inventory_transactions')
            ->where('performable_type', 'App\\Models\\System')
            ->update(['performable_type' => 'App\\Models\\Admin']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Optionally revert if needed
        DB::table('inventory_transactions')
            ->where('performable_type', 'App\\Models\\Admin')
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('admin')
                    ->whereColumn('admin.id', 'inventory_transactions.performable_id');
            })
            ->update(['performable_type' => 'App\\Models\\System']);
    }
};

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixInventoryTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:inventory-transactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invalid performable_type in inventory_transactions table';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing inventory transactions...');

        // Fix local database
        $localCount = DB::table('inventory_transactions')
            ->where('performable_type', 'App\Models\System')
            ->count();

        if ($localCount > 0) {
            DB::table('inventory_transactions')
                ->where('performable_type', 'App\Models\System')
                ->update(['performable_type' => 'App\Models\Admin']);
            $this->info("LOCAL DB: Fixed {$localCount} records");
        } else {
            $this->info('LOCAL DB: No invalid records found');
        }

        // Fix online database
        try {
            $onlineCount = DB::connection('pgsql_online')
                ->table('inventory_transactions')
                ->where('performable_type', 'App\Models\System')
                ->count();

            if ($onlineCount > 0) {
                DB::connection('pgsql_online')
                    ->table('inventory_transactions')
                    ->where('performable_type', 'App\Models\System')
                    ->update(['performable_type' => 'App\Models\Admin']);
                $this->info("ONLINE DB: Fixed {$onlineCount} records");
            } else {
                $this->info('ONLINE DB: No invalid records found');
            }
        } catch (\Exception $e) {
            $this->error('ONLINE DB: ' . $e->getMessage());
        }

        $this->info('Done!');
        return 0;
    }
}

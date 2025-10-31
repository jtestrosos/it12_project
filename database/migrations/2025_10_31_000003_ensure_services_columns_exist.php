<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('services')) {
            if (!Schema::hasColumn('services', 'day_of_week')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->unsignedTinyInteger('day_of_week')->nullable()->after('description');
                });
            }
            if (!Schema::hasColumn('services', 'active')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->boolean('active')->default(true)->after('day_of_week');
                });
            }
            if (!Schema::hasColumn('services', 'description')) {
                Schema::table('services', function (Blueprint $table) {
                    $table->text('description')->nullable()->after('name');
                });
            }
        }
    }

    public function down(): void
    {
        // No-op safe down; removing columns conditionally could lose data.
    }
};



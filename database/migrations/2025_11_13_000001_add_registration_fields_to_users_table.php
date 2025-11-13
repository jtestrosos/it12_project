<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('barangay_other')->nullable()->after('barangay');
            $table->string('purok')->nullable()->after('barangay_other');
            $table->date('birth_date')->nullable()->after('purok');
            $table->unsignedTinyInteger('age')->nullable()->after('birth_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['barangay_other', 'purok', 'birth_date', 'age']);
        });
    }
};


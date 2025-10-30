<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('patient_address')->nullable()->change();
        });
    }
    public function down()
    {
        Schema::table('appointments', function (Blueprint $table) {
            $table->string('patient_address')->nullable(false)->change();
        });
    }
};

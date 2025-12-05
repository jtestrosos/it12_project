<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('created_by_admin_id')->nullable();
            $table->unsignedBigInteger('created_by_super_admin_id')->nullable();
            $table->string('type'); // database, files, full
            $table->string('filename')->nullable();
            $table->string('file_path')->nullable();
            $table->string('size')->nullable(); // e.g., "45.2 MB"
            $table->enum('status', ['in_progress', 'completed', 'failed'])->default('in_progress');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('created_by_admin_id')->references('id')->on('admin')->onDelete('set null');
            $table->foreign('created_by_super_admin_id')->references('id')->on('super_admin')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};

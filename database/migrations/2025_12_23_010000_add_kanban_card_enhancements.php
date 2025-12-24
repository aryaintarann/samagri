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
        // Attachments for Kanban cards
        Schema::create('kanban_card_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('card_id')->constrained('kanban_cards')->onDelete('cascade');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('file_type');
            $table->unsignedBigInteger('file_size');
            $table->timestamps();
        });

        // Multiple assignees for Kanban cards (many-to-many)
        Schema::create('kanban_card_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kanban_card_id')->constrained('kanban_cards')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['kanban_card_id', 'user_id']);
        });

        // Remove the old single assigned_to column
        Schema::table('kanban_cards', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->dropColumn('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back the assigned_to column
        Schema::table('kanban_cards', function (Blueprint $table) {
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
        });

        Schema::dropIfExists('kanban_card_user');
        Schema::dropIfExists('kanban_card_attachments');
    }
};

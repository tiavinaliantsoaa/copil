<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('communication_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('period_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('owner')->default('Responsable Communication');
            $table->date('due_date')->nullable();
            $table->enum('status', ['À faire', 'En cours', 'Bloqué', 'Terminé'])->default('À faire');
            $table->json('summary');
            $table->json('channels');
            $table->json('graphics');
            $table->json('training');
            $table->json('tools');
            $table->json('events');
            $table->json('projects');
            $table->json('action_plan');
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('communication_reports');
    }
};

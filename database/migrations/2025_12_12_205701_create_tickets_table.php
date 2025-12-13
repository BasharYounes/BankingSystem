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
        Schema::create('tickets', function (Blueprint $table) {
        $table->id();

        $table->foreignId('account_id')
            ->constrained('accounts')
            ->onDelete('cascade');

        $table->foreignId('user_id')
            ->constrained('users')
            ->onDelete('cascade');

        $table->string('title');

        $table->enum('category', [
            'general',
            'transaction_issue',
            'account_issue',
            'loan_issue',
            'card_issue',
            'investment_issue',
            'technical'
        ])->default('general');

        $table->enum('priority', ['low', 'normal', 'high', 'urgent'])
            ->default('normal');

        $table->enum('status', [
            'open',
            'pending',
            'processing',
            'escalated',
            'resolved',
            'closed'
        ])->default('open');

        $table->timestamp('resolved_at')->nullable();

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

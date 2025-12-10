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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->enum('type', ['deposit', 'withdrawal', 'transfer', 'loan_payment', 'sellAsset', 'buyAsset']);

            // البيانات المالية
            $table->decimal('amount', 15, 2);
            $table->decimal('fees', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2)->default(0);
            $table->string('currency', 3)->default('USD');

            // الحالة
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'requires_approval'])->default('pending');

            // العلاقات
            $table->foreignId('from_account_id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->foreignId('to_account_id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->foreignId('account_id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');

            // معلومات إضافية
            $table->text('description')->nullable();
            $table->json('metadata')->nullable();
            $table->string('reference_number')->nullable();
            $table->enum('priority', ['normal', 'high', 'urgent'])->default('normal');

            // الموافقات
            $table->boolean('requires_approval')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();

            // التواريخ
            $table->timestamp('executed_at')->nullable();
            $table->timestamp('scheduled_for')->nullable();
            $table->timestamp('processed_at')->nullable();

            // الفحص الأمني
            $table->boolean('is_suspicious')->default(false);
            $table->text('fraud_check_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_transaction');
    }
};

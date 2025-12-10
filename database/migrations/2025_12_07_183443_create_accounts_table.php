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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('account_number');
            $table->double('balance')->default(0);
            $table->enum('type',['savings','checking','loan','investment','composite']);
            $table->enum('status',['active','frozen','suspended','closed'])->default('active');
            $table->enum('component_type', ['individual', 'composite'])->default('individual');
//            $table->unsignedBigInteger('parent_id');
            $table->boolean('is_composite')->default(false);
            $table->date('opening_date');
            $table->date('closing_date')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->decimal('interest_rate', 5, 2)->nullable();
            $table->decimal('overdraft_limit', 15, 2)->default(0);
            $table->decimal('loan_amount', 15, 2)->nullable();
            $table->integer('loan_term_months')->nullable();
            $table->string('risk_level', 20)->nullable();
            $table->decimal('minimum_balance', 15, 2)->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('parent_id')->nullable()->constrained('accounts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('_account');
    }
};

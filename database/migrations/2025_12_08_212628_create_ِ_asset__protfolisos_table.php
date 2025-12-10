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
        Schema::create('ِ_asset__protfolisos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('account_id')
                  ->constrained('accounts')
                  ->onDelete('cascade');
            $table->string('asset_symbol', 15)->index();
            $table->decimal('quantity', 15, 6)->default(0);
            $table->decimal('cost_basis', 15, 4)->default(0);
            $table->unique(['account_id', 'asset_symbol']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ِ_asset__protfolisos');
    }
};

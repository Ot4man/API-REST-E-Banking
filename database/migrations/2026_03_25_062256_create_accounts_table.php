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


            $table->string('type'); // courant | epargne | mineur


            $table->decimal('balance', 10, 2)->default(0);


            $table->string('status')->default('ACTIVE'); 


            $table->decimal('overdraft_limit', 10, 2)->nullable();


            $table->decimal('interest_rate', 5, 2)->nullable();


            $table->foreignId('guardian_id')->nullable()->constrained('users');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};

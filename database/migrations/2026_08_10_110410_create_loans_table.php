<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount_requested', 10, 2);
            $table->decimal('amount_approved', 10, 2)->nullable();
            $table->decimal('interest_rate', 5, 2)->default(0);
            $table->decimal('total_repayable', 10, 2)->nullable();
            $table->decimal('amount_repaid', 10, 2)->default(0);
            $table->decimal('balance', 10, 2)->nullable();
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected', 'repaid', 'defaulted'])->default('pending');
            $table->integer('repayment_months');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('rejection_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loans');
    }
};

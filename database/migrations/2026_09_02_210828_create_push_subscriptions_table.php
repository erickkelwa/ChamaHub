<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('endpoint');
            $table->text('p256dh_key')->nullable();  // Public key for payload encryption
            $table->text('auth_key')->nullable();     // Auth secret
            $table->string('user_agent')->nullable(); // Browser/device info
            $table->timestamps();

            // Prevent duplicate subscriptions for same endpoint per user
            $table->unique(['user_id', 'endpoint'], 'push_user_endpoint_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('push_subscriptions');
    }
};

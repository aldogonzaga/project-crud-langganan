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
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('billing_cycle')->default('Monthly'); // Monthly, Yearly, Weekly
            $table->date('next_billing_date');
            $table->string('category')->default('Entertainment'); // Entertainment, Utilities, Health, Work, Education, Food, Shopping, Others
            $table->string('status')->default('Active'); // Active, Free Trial, Cancelled
            $table->string('icon')->nullable(); // emoji or icon
            $table->string('color')->nullable(); // badge / accent color
            $table->text('notes')->nullable();
            $table->date('trial_ends_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};

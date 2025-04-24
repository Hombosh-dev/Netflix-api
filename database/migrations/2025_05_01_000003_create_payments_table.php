<?php

use App\Enums\PaymentStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->foreignUlid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUlid('tariff_id')->constrained()->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3);
            $table->string('payment_method', 50);
            $table->string('transaction_id', 128)->unique();
            $table->json('liqpay_data')->default(DB::raw("'{}'::json"));
            $table->timestamps();
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->enumAlterColumn('status', 'payment_status', PaymentStatus::class, default: PaymentStatus::PENDING->value);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        DB::unprepared('DROP TYPE payment_status');
    }
};

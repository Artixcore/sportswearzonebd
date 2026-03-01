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
        Schema::table('orders', function (Blueprint $table) {
            $table->decimal('delivery_charge', 12, 2)->default(0)->after('updated_by');
            $table->decimal('delivery_advance_paid', 12, 2)->nullable()->after('delivery_charge');
            $table->string('delivery_advance_method')->nullable()->after('delivery_advance_paid');
            $table->string('delivery_advance_txn_id')->nullable()->after('delivery_advance_method');
            $table->boolean('delivery_advance_customer_confirmed')->default(false)->after('delivery_advance_txn_id');
            $table->string('delivery_advance_admin_txn_id')->nullable()->after('delivery_advance_customer_confirmed');
            $table->boolean('delivery_advance_admin_verified')->default(false)->after('delivery_advance_admin_txn_id');
            $table->string('delivery_settlement_status')->nullable()->default('pending')->after('delivery_advance_admin_verified');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_charge',
                'delivery_advance_paid',
                'delivery_advance_method',
                'delivery_advance_txn_id',
                'delivery_advance_customer_confirmed',
                'delivery_advance_admin_txn_id',
                'delivery_advance_admin_verified',
                'delivery_settlement_status',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('office_supplies', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'minimum_stock_threshold']);

            $table->enum('status', [
                'fully_stocked',
                'in_stock',
                'low_stock',
                'out_of_stock',
            ])->default('in_stock')->after('category');
        });
    }

    public function down(): void
    {
        Schema::table('office_supplies', function (Blueprint $table) {
            $table->dropColumn('status');

            $table->integer('quantity')->default(0);
            $table->integer('minimum_stock_threshold')->default(0);
        });
    }
};

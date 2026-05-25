<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('office_supplies');

        Schema::create('office_supplies', function (Blueprint $table) {
            $table->increments('supply_id');

            $table->string('supply_name', 100);

            $table->enum('category', [
                'Stationery',
                'Cleaning',
                'Ink & Toner',
                'Cables',
                'Tools',
                'Other',
            ])->default('Other');

            $table->integer('quantity')->default(0);
            $table->integer('minimum_stock_threshold')->default(0);

            $table->string('unit', 30)->nullable();
            $table->string('remarks', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('office_supplies');
    }
};

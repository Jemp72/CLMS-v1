<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('equipments');

        Schema::create('equipments', function (Blueprint $table) {
            $table->increments('equipment_id');

            $table->string('equipment_no', 50)->unique();
            $table->string('serial_no', 100)->unique()->nullable();

            $table->string('equipment_name', 150);
            $table->string('brand', 100)->nullable();
            $table->string('model_number', 100)->nullable();

            $table->enum('equipment_type', [
                'computer_unit',
                'peripheral',
                'component',
                'miscellaneous',
            ]);

            $table->enum('equipment_status', [
                'available',
                'in-use',
                'maintenance',
                'damaged',
            ])->default('available');

            $table->integer('quantity')->default(1);

            $table->integer('lab_id');           // signed INT — matches laboratories.lab_id
            $table->integer('parent_equipment_id')->nullable();

            $table->boolean('preventive_maintenance_done')->default(false);
            $table->string('remarks', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipments');
    }
};

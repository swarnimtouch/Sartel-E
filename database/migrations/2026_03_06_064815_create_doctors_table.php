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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id'); // employee reference

            $table->string('doctor_name');
            $table->string('speciality')->nullable();
            $table->string('hospital_name')->nullable();
            $table->string('photo')->nullable();
            $table->string('msl_number')->nullable()->after('doctor_name');
            $table->date('birth_date')->nullable()->after('hospital_name');


            $table->timestamps();

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};

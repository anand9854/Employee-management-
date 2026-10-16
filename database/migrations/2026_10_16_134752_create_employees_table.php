<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('department');
            $table->decimal('salary', 12, 2);
            $table->date('joining_date');
            $table->timestamps();

            $table->index('name');
            $table->index('department');
            $table->index('salary');
            $table->index('joining_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }

};

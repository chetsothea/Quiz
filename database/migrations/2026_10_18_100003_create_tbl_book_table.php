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
        Schema::create('tblBook', function (Blueprint $table) {
            $table->id('BookID');
            $table->string('BookTitle', 255);
            $table->foreignId('BookTypeID')
                ->constrained('tblBookType', 'BookTypeID')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->date('PublishDate')->nullable();
            $table->integer('NumOfPages')->nullable();
            $table->integer('NumOfCopies')->default(0);
            $table->string('Edition', 50)->nullable();
            $table->string('Publisher', 100)->nullable();
            $table->string('BookSource', 100)->nullable();
            $table->text('Remark')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblBook');
    }
};

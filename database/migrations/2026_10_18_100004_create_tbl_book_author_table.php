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
        // Composite primary key (BookID, AuthorID), as in the diagram
        Schema::create('tblBookAuthor', function (Blueprint $table) {
            $table->foreignId('BookID')
                ->constrained('tblBook', 'BookID')
                ->cascadeOnDelete();
            $table->foreignId('AuthorID')
                ->constrained('tblAuthor', 'AuthorID')
                ->cascadeOnDelete();
            $table->date('AuthorDate')->nullable();
            $table->text('Remark')->nullable();
            $table->timestamps();

            $table->primary(['BookID', 'AuthorID']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tblBookAuthor');
    }
};

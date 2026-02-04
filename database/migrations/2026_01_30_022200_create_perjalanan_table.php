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
        Schema::create('perjalanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perjadin_id')->constrained('perjadin')->onDelete('cascade');
            $table->date('tanggal_mulai'); // Start date
            $table->date('tanggal_selesai'); // End date
            $table->string('kota', 200); // Destination city
            $table->string('notadinas', 200); // Nota dinas number
            $table->integer('durasi')->default(1); // Duration in days
            $table->enum('jenis', ['DN', 'DK', 'DLN'])->default('DN'); // Trip type
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perjalanan');
    }
};

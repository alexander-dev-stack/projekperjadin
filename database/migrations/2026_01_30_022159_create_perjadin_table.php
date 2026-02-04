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
        Schema::create('perjadin', function (Blueprint $table) {
            $table->id();
            $table->integer('no')->nullable(); // Order number as per user SQL
            $table->string('nama', 255); // Employee name
            $table->text('unit_kerja')->nullable(); // Work unit (using TEXT as per user SQL)
            $table->integer('sppd_dn')->default(0); 
            $table->integer('sppd_dk')->default(0); 
            $table->integer('sppd_dln')->default(0); 
            $table->integer('hari_dn')->default(0); 
            $table->integer('hari_dk')->default(0); 
            $table->integer('hari_dln')->default(0); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perjadin');
    }
};

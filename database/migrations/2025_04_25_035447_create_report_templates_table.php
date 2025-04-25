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
        Schema::create('report_templates', function (Blueprint $table) {
            $table->id(); // Creates an auto-incrementing primary key column named 'id'
            $table->string('name'); // Column to store the template's name (e.g., "Laporan Harian Pagi")
            $table->text('content'); // Column to store the template content with placeholders. TEXT allows for longer strings.
            $table->timestamps(); // Creates 'created_at' and 'updated_at' columns
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_templates');
    }
};

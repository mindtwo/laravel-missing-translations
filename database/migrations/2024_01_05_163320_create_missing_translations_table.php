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
        if (Schema::hasTable('missing_translations')) {
            return;
        }

        Schema::create('missing_translations', function (Blueprint $table) {
            $table->id();
            $table->string('hash')->unique();
            $table->text('string');
            $table->string('locale', 25);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missing_translations');
    }
};

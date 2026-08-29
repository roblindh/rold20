<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('search_index');
        Schema::create('search_index', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('category', 50);
            $table->string('url', 255);
            $table->text('snippet')->nullable();
            $table->longText('content')->nullable();
            $table->index(['category', 'title']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('search_index');
    }
};

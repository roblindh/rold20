<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            if (!Schema::hasColumn('characters', 'Player')) {
                $table->integer('Player')->nullable()->after('Name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            if (Schema::hasColumn('characters', 'Player')) {
                $table->dropColumn('Player');
            }
        });
    }
};

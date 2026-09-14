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
        if (Schema::hasTable('characters') && !Schema::hasColumn('characters', 'ImagePath')) {
            Schema::table('characters', function (Blueprint $table) {
                $table->string('ImagePath', 255)->nullable()->after('Name');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('characters') && Schema::hasColumn('characters', 'ImagePath')) {
            Schema::table('characters', function (Blueprint $table) {
                $table->dropColumn('ImagePath');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            if (!Schema::hasColumn('characters', 'Alignment')) {
                $table->string('Alignment', 50)->nullable()->after('Gender');
            }
            if (!Schema::hasColumn('characters', 'Religion')) {
                $table->integer('Religion')->nullable()->after('Contacts');
            }
            if (!Schema::hasColumn('characters', 'Deity')) {
                $table->integer('Deity')->nullable()->after('Religion');
            }
            if (!Schema::hasColumn('characters', 'Reputation')) {
                $table->integer('Reputation')->nullable()->after('Deity');
            }
        });
    }

    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $cols = ['Alignment', 'Religion', 'Deity', 'Reputation'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('characters', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            if (!Schema::hasColumn('characters', 'IsNPC')) {
                $table->boolean('IsNPC')->default(0)->after('Player');
            }
            if (!Schema::hasColumn('characters', 'ConfigString')) {
                $table->text('ConfigString')->nullable()->after('IsNPC');
            }
            if (!Schema::hasColumn('characters', 'StatBlock')) {
                $table->text('StatBlock')->nullable()->after('ConfigString');
            }
        });
    }

    public function down(): void
    {
        Schema::table('characters', function (Blueprint $table) {
            $cols = ['IsNPC', 'ConfigString', 'StatBlock'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('characters', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};

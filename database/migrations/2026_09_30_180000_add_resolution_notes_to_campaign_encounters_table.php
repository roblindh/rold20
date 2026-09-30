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
        if (Schema::hasTable('campaign_encounters')) {
            Schema::table('campaign_encounters', function (Blueprint $table) {
                if (!Schema::hasColumn('campaign_encounters', 'resolution_notes')) {
                    $table->longText('resolution_notes')->nullable()->after('status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('campaign_encounters')) {
            Schema::table('campaign_encounters', function (Blueprint $table) {
                if (Schema::hasColumn('campaign_encounters', 'resolution_notes')) {
                    $table->dropColumn('resolution_notes');
                }
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('campaigns')) {
            Schema::table('campaigns', function (Blueprint $table) {
                if (!Schema::hasColumn('campaigns', 'Vault')) {
                    $table->longText('Vault')->nullable()->after('Notes');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('campaigns')) {
            Schema::table('campaigns', function (Blueprint $table) {
                if (Schema::hasColumn('campaigns', 'Vault')) {
                    $table->dropColumn('Vault');
                }
            });
        }
    }
};

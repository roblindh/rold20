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
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns', 'PartyLocation')) {
                $table->string('PartyLocation', 100)->default('Small town')->nullable()->after('Notes');
            }
            if (!Schema::hasColumn('campaigns', 'PartyLocationID')) {
                $table->unsignedInteger('PartyLocationID')->nullable()->after('PartyLocation');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (Schema::hasColumn('campaigns', 'PartyLocationID')) {
                $table->dropColumn('PartyLocationID');
            }
            if (Schema::hasColumn('campaigns', 'PartyLocation')) {
                $table->dropColumn('PartyLocation');
            }
        });
    }
};

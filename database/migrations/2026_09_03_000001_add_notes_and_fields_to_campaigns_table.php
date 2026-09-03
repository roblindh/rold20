<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (!Schema::hasColumn('campaigns', 'Notes')) {
                $table->text('Notes')->nullable()->after('OptionalRules');
            }
            if (!Schema::hasColumn('campaigns', 'AbilityGenMethod')) {
                $table->integer('AbilityGenMethod')->nullable()->default(2)->after('GameMaster');
            }
            if (!Schema::hasColumn('campaigns', 'StartingXP')) {
                $table->integer('StartingXP')->nullable()->default(0)->after('AbilityGenMethod');
            }
            if (!Schema::hasColumn('campaigns', 'SuitabilityLevel')) {
                $table->integer('SuitabilityLevel')->nullable()->default(3)->after('StartingXP');
            }
            if (!Schema::hasColumn('campaigns', 'OptionalRules')) {
                $table->string('OptionalRules', 500)->nullable()->after('SuitabilityLevel');
            }
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            if (Schema::hasColumn('campaigns', 'Notes')) {
                $table->dropColumn('Notes');
            }
        });
    }
};

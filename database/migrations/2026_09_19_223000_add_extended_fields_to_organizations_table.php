<?php
declare(strict_types=1);

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
        Schema::table('ref_organizations', function (Blueprint $table) {
            if (!Schema::hasColumn('ref_organizations', 'InspirationalNames')) {
                $table->text('InspirationalNames')->nullable()->after('Type');
            }
            if (!Schema::hasColumn('ref_organizations', 'Description')) {
                $table->text('Description')->nullable()->after('InspirationalNames');
            }
            if (!Schema::hasColumn('ref_organizations', 'TypicalMembers')) {
                $table->text('TypicalMembers')->nullable()->after('Description');
            }
            if (!Schema::hasColumn('ref_organizations', 'MemberBenefits')) {
                $table->text('MemberBenefits')->nullable()->after('TypicalMembers');
            }
            if (!Schema::hasColumn('ref_organizations', 'MemberResponsibilities')) {
                $table->text('MemberResponsibilities')->nullable()->after('MemberBenefits');
            }
            if (!Schema::hasColumn('ref_organizations', 'UsesOfInfluence')) {
                $table->text('UsesOfInfluence')->nullable()->after('MemberResponsibilities');
            }
            if (!Schema::hasColumn('ref_organizations', 'RanksAndTitles')) {
                $table->text('RanksAndTitles')->nullable()->after('UsesOfInfluence');
            }
            if (!Schema::hasColumn('ref_organizations', 'SocialClassRange')) {
                $table->string('SocialClassRange', 100)->nullable()->after('RanksAndTitles');
            }
            if (!Schema::hasColumn('ref_organizations', 'WealthClassRange')) {
                $table->string('WealthClassRange', 100)->nullable()->after('SocialClassRange');
            }
            if (!Schema::hasColumn('ref_organizations', 'FavoredSkills')) {
                $table->text('FavoredSkills')->nullable()->after('WealthClassRange');
            }
            if (!Schema::hasColumn('ref_organizations', 'Alignment')) {
                $table->string('Alignment', 50)->nullable()->after('FavoredSkills');
            }
            if (!Schema::hasColumn('ref_organizations', 'Scale')) {
                $table->string('Scale', 50)->nullable()->after('Alignment');
            }
        });

        Schema::table('ref_organizationtypes', function (Blueprint $table) {
            if (!Schema::hasColumn('ref_organizationtypes', 'Description')) {
                $table->text('Description')->nullable()->after('Type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ref_organizations', function (Blueprint $table) {
            $columns = [
                'InspirationalNames', 'Description', 'TypicalMembers',
                'MemberBenefits', 'MemberResponsibilities', 'UsesOfInfluence',
                'RanksAndTitles', 'SocialClassRange', 'WealthClassRange',
                'FavoredSkills', 'Alignment', 'Scale'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('ref_organizations', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('ref_organizationtypes', function (Blueprint $table) {
            if (Schema::hasColumn('ref_organizationtypes', 'Description')) {
                $table->dropColumn('Description');
            }
        });
    }
};

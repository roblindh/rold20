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
        // 1. Campaign Locations Table (Settlements, Taverns, Shops, Dungeons, Ruins, Wilderness, etc.)
        if (!Schema::hasTable('campaign_locations')) {
            Schema::create('campaign_locations', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('campaign_id')->unsigned();
                $table->integer('parent_location_id')->unsigned()->nullable();
                $table->string('name', 150);
                $table->string('location_type', 50)->default('settlement'); // settlement, tavern, shop, dungeon, wilderness, temple, ruin, stronghold
                $table->text('summary')->nullable();
                $table->text('description')->nullable();
                $table->text('sensory_details')->nullable();
                $table->json('notable_npcs')->nullable();
                $table->json('inventory_and_services')->nullable();
                $table->json('rumors_and_hooks')->nullable();
                $table->longText('gm_notes')->nullable();
                $table->timestamps();

                $table->foreign('campaign_id')->references('ID')->on('campaigns')->onDelete('cascade');
            });
        }

        // 2. Campaign Adventures Table
        if (!Schema::hasTable('campaign_adventures')) {
            Schema::create('campaign_adventures', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('campaign_id')->unsigned();
                $table->string('name', 150);
                $table->text('synopsis')->nullable();
                $table->enum('status', ['planning', 'active', 'completed', 'archived'])->default('planning');
                $table->unsignedTinyInteger('min_level')->default(1);
                $table->unsignedTinyInteger('max_level')->default(20);
                $table->integer('order_index')->default(0);
                $table->longText('gm_notes')->nullable();
                $table->timestamps();

                $table->foreign('campaign_id')->references('ID')->on('campaigns')->onDelete('cascade');
            });
        }

        // 3. Campaign Encounters Table
        if (!Schema::hasTable('campaign_encounters')) {
            Schema::create('campaign_encounters', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('campaign_id')->unsigned();
                $table->integer('adventure_id')->unsigned()->nullable();
                $table->integer('location_id')->unsigned()->nullable();
                $table->string('name', 150);
                $table->string('type', 50)->default('combat'); // combat, social, trap_hazard, puzzle, exploration
                $table->decimal('encounter_level', 4, 1)->default(1.0);
                $table->string('environment', 150)->nullable();
                $table->text('description')->nullable();
                $table->text('tactics_and_features')->nullable();
                $table->json('monsters_and_npcs')->nullable();
                $table->json('traps_and_hazards')->nullable();
                $table->json('treasure_rewards')->nullable();
                $table->unsignedInteger('xp_award')->default(0);
                $table->enum('status', ['planned', 'in_progress', 'completed', 'bypassed'])->default('planned');
                $table->integer('order_index')->default(0);
                $table->longText('gm_notes')->nullable();
                $table->timestamps();

                $table->foreign('campaign_id')->references('ID')->on('campaigns')->onDelete('cascade');
                $table->foreign('adventure_id')->references('id')->on('campaign_adventures')->onDelete('set null');
                $table->foreign('location_id')->references('id')->on('campaign_locations')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_encounters');
        Schema::dropIfExists('campaign_adventures');
        Schema::dropIfExists('campaign_locations');
    }
};

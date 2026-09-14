<?php
declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $commonActionIds = [
            38,  // Dodging
            41,  // Land Softly
            42,  // Maintain Balance
            49,  // Jump
            50,  // Climb
            51,  // Swim or Dive
            59,  // Bind Wounds
            61,  // Resuscitate
            70,  // Listen
            72,  // Search
            74,  // Spot
            81,  // Gather Information
            82,  // Haggle
            86,  // Sense Motive
            93,  // Hide
            94,  // Move Silently
            113, // Break Barrier
            208, // Use Influence
            298, // Smell
        ];

        // Reset any existing >= 2 back to 1
        DB::table('ref_actions')->where('ShowPCGen', '>=', 2)->update(['ShowPCGen' => 1]);

        // Set ShowPCGen = 2 for the curated common actions
        DB::table('ref_actions')->whereIn('ID', $commonActionIds)->update(['ShowPCGen' => 2]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('ref_actions')->where('ShowPCGen', 2)->update(['ShowPCGen' => 1]);
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\MstObjective;
use App\Models\MstPractice;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run synchronization if we are running on the local connection
        // to avoid cloud database querying itself.
        $defaultConnection = config('database.default');
        
        if ($defaultConnection !== 'cloud') {
            try {
                // 1. Sync Objectives
                $cloudObjectives = MstObjective::on('cloud')->get();
                foreach ($cloudObjectives as $obj) {
                    MstObjective::firstOrCreate(
                        ['objective_id' => $obj->objective_id],
                        [
                            'domain' => $obj->domain,
                            'objective' => $obj->objective
                        ]
                    );
                }

                // 2. Sync Practices
                $cloudPractices = MstPractice::on('cloud')->get();
                foreach ($cloudPractices as $prac) {
                    MstPractice::firstOrCreate(
                        ['practice_id' => $prac->practice_id],
                        [
                            'objective_id' => $prac->objective_id,
                            'practice_name' => $prac->practice_name
                        ]
                    );
                }
            } catch (\Exception $e) {
                // Log or handle exception if cloud connection fails during local migrate
                \Illuminate\Support\Facades\Log::warning("Could not sync from cloud: " . $e->getMessage());
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op
    }
};

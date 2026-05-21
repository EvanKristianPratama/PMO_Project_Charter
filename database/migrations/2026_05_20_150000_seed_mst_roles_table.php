<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\MstRole;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $roles = [
            'Dewan Direksi (Board)',
            'Executive Committee',
            'Chief Executive Officer',
            'Chief Risk Officer',
            'Chief Operating Officer',
            'Chief Information Officer',
            'Chief Information Security Officer',
            'Chief Technology Officer',
            'Chief Digital Officer',
            'Chief Financial Officer',
            'I&T Governance Board',
            'Architecture Board',
            'Steering (Programs/Projects) Committee',
            'Enterprise Risk Committee',
            'Portfolio Manager',
            'Relationship Manager',
            'Service Manager',
            'Program Manager',
            'Project Manager',
            'Information Security Manager',
            'Business Continuity Manager',
            'Business Process Owners',
            'Data Management Functions',
            'Head Architect',
            'Head Development',
            'Head IT Operations',
            'Project Management Office',
            'Head Human Resources',
            'Head IT Administration',
            'Privacy Officer',
            'Legal Counsel',
            'Compliance',
            'Audit',
        ];

        foreach ($roles as $roleName) {
            // Check if role already exists by name (case-insensitive check to be safe)
            $exists = MstRole::whereRaw('LOWER(name) = ?', [strtolower($roleName)])->exists();
            
            if (!$exists) {
                MstRole::create([
                    'name' => $roleName,
                    'description' => null,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down action needed for seeding
    }
};

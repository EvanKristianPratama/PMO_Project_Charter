<?php

namespace Database\Seeders;

use App\Models\PhaseDigital;
use App\Models\StatusDigital;
use Illuminate\Database\Seeder;

class StatusDigitalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $phasePropose = PhaseDigital::where('name', 'propose')->first();
        $phaseReview = PhaseDigital::where('name', 'review')->first();
        $phaseApprove = PhaseDigital::where('name', 'approve')->first();
        $phaseFinish = PhaseDigital::where('name', 'finish')->first();

        $proposeStatuses = ['drafting', 'submitted', 'canceled'];
        $reviewStatuses = ['on review', 'perlu perbaikan', 'hold', 'reject', 'accepted'];
        $approveStatuses = ['on process', 'accept', 'reject'];
        $finishStatuses = ['baseline', 'hold'];

        foreach ($proposeStatuses as $name) {
            StatusDigital::updateOrCreate(
                ['phase_id' => $phasePropose->id, 'name' => $name],
                ['phase_id' => $phasePropose->id, 'name' => $name]
            );
        }

        foreach ($reviewStatuses as $name) {
            StatusDigital::updateOrCreate(
                ['phase_id' => $phaseReview->id, 'name' => $name],
                ['phase_id' => $phaseReview->id, 'name' => $name]
            );
        }

        foreach ($approveStatuses as $name) {
            StatusDigital::updateOrCreate(
                ['phase_id' => $phaseApprove->id, 'name' => $name],
                ['phase_id' => $phaseApprove->id, 'name' => $name]
            );
        }

        foreach ($finishStatuses as $name) {
            StatusDigital::updateOrCreate(
                ['phase_id' => $phaseFinish->id, 'name' => $name],
                ['phase_id' => $phaseFinish->id, 'name' => $name]
            );
        }
    }
}

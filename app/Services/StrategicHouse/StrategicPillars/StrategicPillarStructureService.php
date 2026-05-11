<?php

namespace App\Services\StrategicHouse\StrategicPillars;

use App\Models\Goal;
use App\Models\InitiativeTagging;
use App\Models\Theme;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;

class StrategicPillarStructureService
{
    public function createGoal(array $payload): Goal
    {
        $goal = new Goal();

        return $this->persistGoal($goal, $payload);
    }

    public function updateGoal(Goal $goal, array $payload): Goal
    {
        $this->ensureGoalMatchesPilar($goal, (string) $payload['pilar']);

        $previousCode = $goal->code;
        $goal = $this->persistGoal($goal, $payload);

        if ($previousCode !== $goal->code) {
            InitiativeTagging::query()
                ->where('pilar', $goal->pilar)
                ->where('goal', $previousCode)
                ->update(['goal' => $goal->code]);
        }

        return $goal;
    }

    public function deleteGoal(Goal $goal, string $pilar): void
    {
        $this->ensureGoalMatchesPilar($goal, $pilar);

        if ($goal->themes()->exists()) {
            throw ValidationException::withMessages([
                'general' => 'Blok ini masih memiliki theme. Hapus theme terkait terlebih dahulu.',
            ]);
        }

        if ($this->goalTaggingsQuery($goal)->exists()) {
            throw ValidationException::withMessages([
                'general' => 'Blok ini masih dipakai oleh initiative tagging dan belum bisa dihapus.',
            ]);
        }

        $goal->delete();
    }

    public function createTheme(array $payload): Theme
    {
        $goal = $this->findGoalForPilar((int) $payload['idGoal'], (string) $payload['pilar']);

        $theme = new Theme();
        $theme->fill([
            'idGoal' => $goal->id,
            'theme_number' => (int) $payload['theme_number'],
            'name' => trim((string) $payload['name']),
        ]);
        $theme->save();

        return $theme->load('goal:id,code,title,pilar');
    }

    public function updateTheme(Theme $theme, array $payload): Theme
    {
        $theme->loadMissing('goal:id,code,pilar');
        $this->ensureThemeMatchesPilar($theme, (string) $payload['pilar']);

        $goal = $this->findGoalForPilar((int) $payload['idGoal'], (string) $payload['pilar']);

        if ((int) $theme->idGoal !== (int) $goal->id && $theme->initiativeTaggings()->exists()) {
            throw ValidationException::withMessages([
                'idGoal' => 'Theme yang sudah dipakai tagging tidak bisa dipindahkan ke blok lain.',
            ]);
        }

        $theme->fill([
            'idGoal' => $goal->id,
            'theme_number' => (int) $payload['theme_number'],
            'name' => trim((string) $payload['name']),
        ]);
        $theme->save();

        return $theme->load('goal:id,code,title,pilar');
    }

    public function deleteTheme(Theme $theme, string $pilar): void
    {
        $theme->loadMissing('goal:id,code,pilar');
        $this->ensureThemeMatchesPilar($theme, $pilar);

        if ($theme->initiativeTaggings()->exists()) {
            throw ValidationException::withMessages([
                'general' => 'Theme ini masih dipakai oleh initiative tagging dan belum bisa dihapus.',
            ]);
        }

        $theme->delete();
    }

    private function persistGoal(Goal $goal, array $payload): Goal
    {
        $goal->fill([
            'code' => trim((string) $payload['code']),
            'title' => trim((string) $payload['title']),
            'pilar' => (string) $payload['pilar'],
        ]);

        try {
            $goal->save();
        } catch (QueryException $exception) {
            if ((int) $exception->getCode() === 23000) {
                throw ValidationException::withMessages([
                    'code' => 'Kode blok ini masih bentrok dengan constraint database yang lama. Jalankan migration pelepas unique code pada mst_goals agar kode yang sama bisa dipakai di pilar berbeda.',
                ]);
            }

            throw $exception;
        }

        return $goal->load(['themes' => fn ($query) => $query->orderBy('theme_number')]);
    }

    private function findGoalForPilar(int $goalId, string $pilar): Goal
    {
        $goal = Goal::query()
            ->whereKey($goalId)
            ->where('pilar', $pilar)
            ->first();

        if (!$goal) {
            throw ValidationException::withMessages([
                'idGoal' => 'Blok yang dipilih tidak ditemukan pada pilar ini.',
            ]);
        }

        return $goal;
    }

    private function ensureGoalMatchesPilar(Goal $goal, string $pilar): void
    {
        if ((string) $goal->pilar !== $pilar) {
            throw ValidationException::withMessages([
                'general' => 'Blok tidak berada pada pilar yang aktif.',
            ]);
        }
    }

    private function ensureThemeMatchesPilar(Theme $theme, string $pilar): void
    {
        if ((string) $theme->goal?->pilar !== $pilar) {
            throw ValidationException::withMessages([
                'general' => 'Theme tidak berada pada pilar yang aktif.',
            ]);
        }
    }

    private function goalTaggingsQuery(Goal $goal)
    {
        return InitiativeTagging::query()
            ->where('pilar', $goal->pilar)
            ->where('goal', $goal->code);
    }
}

<?php

namespace App\Services\ProgramPlanning\StrategicHouse\InitiativeSupport;

use App\Models\MstCoe;
use App\Models\MstInitiative;
use App\Models\TrsInitiativeSupport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class InitiativeSupportService
{
    public function getPageProps(): array
    {
        return [
            'groups' => $this->getGroupedMappings(),
            'digitalInitiativeOptions' => $this->getInitiativeOptions(1)->all(),
            'itInitiativeOptions' => $this->getInitiativeOptions(2)->all(),
        ];
    }

    public function getGroupedMappings(): array
    {
        return TrsInitiativeSupport::query()
            ->with([
                'digitalInitiative:id,code,name,tipe_initiative,coe_id',
                'digitalInitiative.coe:id,name',
                'itInitiative:id,code,name,tipe_initiative,coe_id',
                'itInitiative.coe:id,name',
            ])
            ->whereHas('digitalInitiative', fn (Builder $query) => $query->where('tipe_initiative', 1))
            ->whereHas('itInitiative', fn (Builder $query) => $query->where('tipe_initiative', 2))
            ->orderBy('id')
            ->get()
            ->groupBy(fn (TrsInitiativeSupport $mapping): string => $this->resolveGroupKey($mapping))
            ->map(fn (Collection $mappings, string $groupKey): array => $this->mapGroup($mappings, $groupKey))
            ->values()
            ->all();
    }

    public function storeMappings(array $data): int
    {
        $digitalIds = collect($data['digital_ids'] ?? [])
            ->map(fn ($value): int => (int) $value)
            ->filter(fn (int $value): bool => $value > 0)
            ->unique()
            ->values();

        $itIds = collect($data['it_ids'] ?? [])
            ->map(fn ($value): int => (int) $value)
            ->filter(fn (int $value): bool => $value > 0)
            ->unique()
            ->values();

        if ($digitalIds->isEmpty() || $itIds->isEmpty()) {
            throw ValidationException::withMessages([
                'digital_ids' => 'Pilih minimal satu Digital Initiative dan satu IT Initiative.',
            ]);
        }

        $existingKeys = TrsInitiativeSupport::query()
            ->whereIn('digital_id', $digitalIds->all())
            ->whereIn('it_id', $itIds->all())
            ->get(['digital_id', 'it_id'])
            ->map(fn (TrsInitiativeSupport $mapping): string => $this->mappingKey($mapping->digital_id, $mapping->it_id))
            ->all();

        $existingKeyLookup = array_fill_keys($existingKeys, true);
        $note = trim((string) ($data['notes'] ?? ''));
        $timestamp = now();
        $payload = [];

        foreach ($digitalIds as $digitalId) {
            foreach ($itIds as $itId) {
                $mappingKey = $this->mappingKey($digitalId, $itId);

                if (isset($existingKeyLookup[$mappingKey])) {
                    continue;
                }

                $payload[] = [
                    'digital_id' => $digitalId,
                    'it_id' => $itId,
                    'notes' => $note !== '' ? $note : null,
                    'created_at' => $timestamp,
                    'updated_at' => $timestamp,
                ];
            }
        }

        if ($payload === []) {
            throw ValidationException::withMessages([
                'digital_ids' => 'Semua pasangan Digital Initiative dan IT Initiative yang dipilih sudah tersedia.',
            ]);
        }

        TrsInitiativeSupport::query()->insert($payload);

        return count($payload);
    }

    public function deleteMappings(array $mappingIds): int
    {
        $ids = collect($mappingIds)
            ->map(fn ($value): int => (int) $value)
            ->filter(fn (int $value): bool => $value > 0)
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return 0;
        }

        return TrsInitiativeSupport::query()
            ->whereIn('id', $ids->all())
            ->delete();
    }

    private function getInitiativeOptions(int $initiativeType): Collection
    {
        return MstInitiative::query()
            ->with(['coe:id,name'])
            ->where('tipe_initiative', $initiativeType)
            ->orderBy('id')
            ->get(['id', 'code', 'name', 'coe_id'])
            ->map(fn (MstInitiative $initiative): array => $this->mapInitiative($initiative));
    }

    private function mapGroup(Collection $mappings, string $groupKey): array
    {
        $note = trim((string) ($mappings->first()?->notes ?? ''));

        $digitalInitiatives = $mappings
            ->map(fn (TrsInitiativeSupport $mapping): ?array => $this->mapInitiative($mapping->digitalInitiative))
            ->filter()
            ->unique('id')
            ->values();

        $itInitiatives = $mappings
            ->map(fn (TrsInitiativeSupport $mapping): ?array => $this->mapInitiative($mapping->itInitiative))
            ->filter()
            ->unique('id')
            ->values();

        return [
            'group_key' => $groupKey,
            'note' => $note,
            'note_label' => $note !== '' ? $note : 'Belum ada catatan dukungan.',
            'digital_initiatives' => $digitalInitiatives->all(),
            'it_initiatives' => $itInitiatives->all(),
            'mapping_ids' => $mappings->pluck('id')->map(fn ($id): int => (int) $id)->values()->all(),
            'mappings' => $mappings
                ->map(fn (TrsInitiativeSupport $mapping): array => [
                    'id' => (int) $mapping->id,
                    'digital_id' => (int) $mapping->digital_id,
                    'it_id' => (int) $mapping->it_id,
                ])
                ->values()
                ->all(),
            'total_mappings' => (int) $mappings->count(),
        ];
    }

    private function mapInitiative(?MstInitiative $initiative): ?array
    {
        if (! $initiative?->id) {
            return null;
        }

        $code = trim((string) $initiative->code);
        $name = $this->displayInitiativeName((string) $initiative->name, $code);

        return [
            'id' => (int) $initiative->id,
            'code' => $code,
            'name' => $name,
            'label' => $this->buildInitiativeLabel($code, $name),
            'coe_name' => trim((string) ($initiative->coe?->name ?? 'No CoE')),
        ];
    }

    private function resolveGroupKey(TrsInitiativeSupport $mapping): string
    {
        return 'digital:'.(int) $mapping->digital_id;
    }

    private function mappingKey(int $digitalId, int $itId): string
    {
        return $digitalId.':'.$itId;
    }

    private function buildInitiativeLabel(string $code, string $name): string
    {
        if ($code !== '' && $name !== '') {
            return sprintf('[%s] %s', $code, $name);
        }

        return $name !== '' ? $name : $code;
    }

    private function displayInitiativeName(string $name, string $code): string
    {
        $rawName = trim($name);

        if ($rawName === '' || $code === '') {
            return $rawName;
        }

        $escapedCode = preg_quote($code, '/');
        $cleanedName = preg_replace("/^\\s*(\\[\\s*)?{$escapedCode}(\\s*\\])?\\s*[-|.:)]?\\s*/i", '', $rawName);
        $cleanedName = trim((string) $cleanedName);

        return $cleanedName !== '' ? $cleanedName : $rawName;
    }
}

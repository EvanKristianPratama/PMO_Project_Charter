<?php

namespace App\Services\ProgramPlanning\StrategicHouse\InitiativeSupport;

use App\Models\MstInitiative;
use App\Models\TrsInitiativeSupport;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
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
        $mappings = TrsInitiativeSupport::query()
            ->with([
                'digitalInitiative.coe',
                'digitalInitiative.organization',
                'itInitiative.coe',
                'itInitiative.organization',
            ])
            ->whereHas('digitalInitiative', fn (Builder $query) => $query->where('tipe_initiative', 1))
            ->whereHas('itInitiative', fn (Builder $query) => $query->where('tipe_initiative', 2))
            ->orderBy('id')
            ->get();

        $digitalGroups = $mappings
            ->groupBy(fn (TrsInitiativeSupport $mapping): string => $this->resolveDigitalNoteKey($mapping))
            ->map(fn (Collection $groupMappings): array => $this->mapDigitalGroup($groupMappings))
            ->values()
            ->all();

        return collect($digitalGroups)
            ->groupBy(fn (array $group): string => $this->resolveSharedSupportKey($group))
            ->map(fn (Collection $groupedDigitals, string $groupKey): array => $this->mapSharedGroup($groupedDigitals, $groupKey))
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
            ->with(['coe', 'organization'])
            ->where('tipe_initiative', $initiativeType)
            ->orderBy('id')
            ->get()
            ->map(fn (MstInitiative $initiative): array => $this->mapInitiative($initiative));
    }

    private function mapDigitalGroup(Collection $mappings): array
    {
        $note = $this->normalizeNote($mappings->first()?->notes);
        $digitalInitiative = $this->mapInitiative($mappings->first()?->digitalInitiative);

        $itInitiatives = $mappings
            ->map(fn (TrsInitiativeSupport $mapping): ?array => $this->mapInitiative($mapping->itInitiative))
            ->filter()
            ->unique('id')
            ->values();

        return [
            'digital_initiative' => $digitalInitiative,
            'digital_id' => (int) ($digitalInitiative['id'] ?? 0),
            'note' => $note,
            'it_initiatives' => $itInitiatives->all(),
            'first_mapping_id' => (int) ($mappings->first()?->id ?? 0),
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

    private function mapSharedGroup(Collection $digitalGroups, string $groupKey): array
    {
        $orderedDigitalGroups = $digitalGroups
            ->sortBy('first_mapping_id')
            ->values();

        $note = $this->normalizeNote($orderedDigitalGroups->first()['note'] ?? null);

        $digitalInitiatives = $orderedDigitalGroups
            ->map(fn (array $group): ?array => $group['digital_initiative'] ?? null)
            ->filter()
            ->unique('id')
            ->values();

        $itInitiatives = $orderedDigitalGroups
            ->flatMap(fn (array $group): array => $group['it_initiatives'] ?? [])
            ->filter()
            ->unique('id')
            ->values();

        $mappingIds = $orderedDigitalGroups
            ->flatMap(fn (array $group): array => $group['mapping_ids'] ?? [])
            ->map(fn ($id): int => (int) $id)
            ->unique()
            ->values();

        return [
            'group_key' => $groupKey,
            'note' => $note,
            'note_label' => $note !== '' ? $note : 'Belum ada catatan dukungan.',
            'digital_initiatives' => $digitalInitiatives->all(),
            'it_initiatives' => $itInitiatives->all(),
            'mapping_ids' => $mappingIds->all(),
            'mappings' => $orderedDigitalGroups
                ->flatMap(fn (array $group): array => $group['mappings'] ?? [])
                ->values()
                ->all(),
            'total_mappings' => $mappingIds->count(),
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
            'business_unit' => trim((string) ($initiative->organization?->name ?? $initiative->business_unit ?? '')),
        ];
    }

    private function resolveDigitalNoteKey(TrsInitiativeSupport $mapping): string
    {
        return 'digital:'.(int) $mapping->digital_id.'|note:'.md5($this->normalizeNote($mapping->notes));
    }

    private function resolveSharedSupportKey(array $group): string
    {
        $itIds = collect($group['it_initiatives'] ?? [])
            ->map(fn (array $initiative): int => (int) ($initiative['id'] ?? 0))
            ->filter(fn (int $id): bool => $id > 0)
            ->sort()
            ->values()
            ->implode('-');

        return 'note:'.md5($this->normalizeNote($group['note'] ?? null)).'|it:'.$itIds;
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

    private function normalizeNote(?string $note): string
    {
        return trim((string) $note);
    }
}

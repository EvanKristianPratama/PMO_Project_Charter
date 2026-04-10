<?php

namespace App\Services\ProgramPlanning\StrategicHouse;

use App\Models\Goal;
use App\Models\MstCoe;
use App\Models\MstInitiative;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StrategicHousePageService
{
    private const DEFAULT_INITIATIVE_TYPE = 1;

    private const TECHNOLOGY_COE_CONFIG = [
        ['name' => 'IoT', 'label' => 'IoT', 'tone' => 'sky'],
        ['name' => 'Cloud & Advanced Computing', 'label' => 'Advance Cloud', 'tone' => 'indigo'],
        ['name' => 'RPA', 'label' => 'RPA', 'tone' => 'cyan'],
        ['name' => 'Robotics', 'label' => 'Robotics', 'tone' => 'slate'],
        ['name' => 'AI/Analytics', 'label' => 'AI / Adv. Analytics', 'tone' => 'amber'],
    ];

    private const STRATEGY_COE_CONFIG = [
        [
            'name' => 'User Interface and Experience',
            'label' => 'User Interface',
            'tone' => 'sky',
            'description_lines' => [
                'Memastikan',
                'standarisasi UI/UX',
                'pada seluruh aplikasi',
            ],
        ],
        [
            'name' => 'Integration and Automation',
            'label' => 'Integration and Automation',
            'tone' => 'blue',
            'description_lines' => [
                'Meningkatkan',
                'interaksi sistem di',
                'seluruh holding,',
                'subholding, dan APFS',
            ],
        ],
        [
            'name' => 'Business Application System',
            'label' => 'Business Application System',
            'tone' => 'indigo',
            'description_lines' => [
                'Merasionalisasi dan',
                'memodernisasi',
                'aplikasi legacy,',
                'termasuk membangun',
                'groupwide ERP',
            ],
        ],
        [
            'name' => 'Infrastructure',
            'label' => 'Infrastructure',
            'tone' => 'cyan',
            'description_lines' => [
                'Membangun',
                'infrastruktur best-in-',
                'class untuk',
                'mendukung',
                'peningkatan',
                'kompleksitas use case',
                'digital',
            ],
        ],
        [
            'name' => 'Data and Analytics',
            'label' => 'Data and Analytics',
            'tone' => 'emerald',
            'description_lines' => [
                'AI platform;',
                'Memastikan',
                'ketersediaan dan',
                'keandalan data untuk',
                'mendukung use case',
                'digital',
            ],
        ],
        [
            'name' => 'Cybersecurity',
            'label' => 'Cybersecurity',
            'tone' => 'slate',
            'description_lines' => [
                'Memperkuat kesiapan',
                'menghadapi cyber',
                'threats yang terus',
                'meningkat',
            ],
        ],
    ];

    private const FOUNDATION_COE_CONFIG = [
        'name' => 'People, Process and Technology',
        'label' => 'People, Process and Technology',
        'tone' => 'foundation',
    ];

    private const ARCHITECTURE_COE_CONFIG = [
        'name' => 'Overall Architecture',
        'label' => 'Overall Architecture',
        'tone' => 'architecture',
    ];

    private const TBC_COE_CONFIG = [
        'name' => 'TBC',
        'label' => 'TBC',
        'tone' => 'support',
    ];

    private const FALLBACK_DUAL_GROWTH_GOALS = [
        ['id' => 'goal-a', 'code' => 'A', 'title' => 'Maximizing Value'],
        ['id' => 'goal-b', 'code' => 'B', 'title' => 'Expand to new markets & adjacencies'],
        ['id' => 'goal-c', 'code' => 'C', 'title' => 'Building low carbon business'],
    ];

    public function getPageProps(array $filters = []): array
    {
        $normalizedFilters = $this->normalizeFilters($filters);
        $initiativeType = $normalizedFilters['initiative_type'];
        $showEmpty = $normalizedFilters['show_empty'];
        $dualGrowthGoals = $this->getDualGrowthGoals();
        $roofSection = $this->getRoofSection();

        $coeCatalog = $this->getCoeCatalog($initiativeType);
        $technologyCards = $this->buildSectionCards($coeCatalog, self::TECHNOLOGY_COE_CONFIG, $showEmpty);
        $strategyCards = $this->buildSectionCards($coeCatalog, self::STRATEGY_COE_CONFIG, $showEmpty);
        $foundationCard = $this->buildSingleCard($coeCatalog, self::FOUNDATION_COE_CONFIG);
        $architectureCard = $this->buildSingleCard($coeCatalog, self::ARCHITECTURE_COE_CONFIG);
        $tbcCard = $this->buildSingleCard($coeCatalog, self::TBC_COE_CONFIG);
        $unassignedInitiatives = $this->getUnassignedInitiatives($initiativeType);

        return [
            'filters' => $normalizedFilters,
            'page' => [
                'title' => 'Strategic House',
                'headline' => 'Pertamina Group Dual Growth Strategy',
                'visionTitle' => 'Visi Pertamina IT',
                'visionText' => 'Meningkatkan peranan IT dari business enabler menjadi strategic value creator, mendorong transformasi digital untuk mendukung ambisi dual growth Pertamina Group.',
                'initiativeLabel' => $initiativeType === 2
                    ? 'IT transformation initiatives'
                    : 'Digital transformation initiatives',
                'grandStrategyTitle' => 'Grand IT Strategy',
                'grandStrategyText' => 'Single source of truth for groupwide IT reference architecture',
            ],
            'summary' => $this->buildSummary(
                $technologyCards,
                $strategyCards,
                $foundationCard,
                $architectureCard,
                $tbcCard,
                $unassignedInitiatives
            ),
            'roofSection' => $roofSection,
            'focusBands' => $this->getFocusBands($dualGrowthGoals),
            'dualGrowthGoals' => $dualGrowthGoals,
            'technologyCards' => $technologyCards,
            'strategyCards' => $strategyCards,
            'foundationCard' => $foundationCard,
            'architectureCard' => $architectureCard,
            'tbcCard' => $tbcCard,
            'unassignedInitiatives' => $unassignedInitiatives,
        ];
    }

    private function normalizeFilters(array $filters): array
    {
        $initiativeType = (int) ($filters['initiative_type'] ?? self::DEFAULT_INITIATIVE_TYPE);

        return [
            'initiative_type' => in_array($initiativeType, [1, 2], true)
                ? $initiativeType
                : self::DEFAULT_INITIATIVE_TYPE,
            'show_empty' => (bool) ($filters['show_empty'] ?? true),
        ];
    }

    private function getCoeCatalog(int $initiativeType): Collection
    {
        return MstCoe::query()
            ->select(['id', 'name'])
            ->with([
                'initiatives' => fn ($query) => $query
                    ->select(['id', 'coe_id', 'code', 'name', 'status'])
                    ->where('tipe_initiative', $initiativeType)
                    ->with('latestStatus')
                    ->orderBy('code'),
            ])
            ->withCount([
                'initiatives as initiatives_count' => fn ($query) => $query
                    ->where('tipe_initiative', $initiativeType),
            ])
            ->orderBy('id')
            ->get()
            ->mapWithKeys(fn (MstCoe $coe): array => [
                Str::lower($coe->name) => $this->mapCoeCard($coe),
            ]);
    }

    private function buildSectionCards(Collection $coeCatalog, array $configs, bool $showEmpty): array
    {
        return collect($configs)
            ->map(fn (array $config): ?array => $this->buildSingleCard($coeCatalog, $config))
            ->filter(fn (?array $card): bool => $card !== null && ($showEmpty || ! $card['is_empty']))
            ->values()
            ->all();
    }

    private function buildSingleCard(Collection $coeCatalog, array $config): ?array
    {
        $baseCard = $coeCatalog->get(Str::lower($config['name']));

        if ($baseCard === null) {
            return null;
        }

        return [
            ...$baseCard,
            'display_name' => $config['label'] ?? $baseCard['display_name'],
            'tone' => $config['tone'] ?? $baseCard['tone'],
            'description_lines' => $config['description_lines'] ?? [],
        ];
    }

    private function mapCoeCard(MstCoe $coe): array
    {
        $initiatives = ($coe->initiatives ?? collect())
            ->map(function (MstInitiative $initiative): array {
                $status = $this->normalizeStatus($initiative->latestPlanningStatusValue());

                return [
                    'id' => (int) $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                    'label' => trim(collect([$initiative->code, $initiative->name])->filter()->implode(' - ')),
                    'status' => $status,
                    'status_label' => $this->statusLabel($status),
                ];
            })
            ->values();

        return [
            'id' => (int) $coe->id,
            'name' => $coe->name,
            'display_name' => $coe->name,
            'tone' => 'default',
            'initiatives_count' => $initiatives->count(),
            'is_empty' => $initiatives->isEmpty(),
            'initiatives_preview' => $initiatives->take(4)->values()->all(),
            'status_breakdown' => $this->buildStatusBreakdown($initiatives),
        ];
    }

    private function buildSummary(
        array $technologyCards,
        array $strategyCards,
        ?array $foundationCard,
        ?array $architectureCard,
        ?array $tbcCard,
        array $unassignedInitiatives
    ): array {
        $allCards = collect([
            ...$technologyCards,
            ...$strategyCards,
            ...array_filter([$foundationCard, $architectureCard, $tbcCard]),
        ]);

        $totalMappedInitiatives = (int) $allCards->sum('initiatives_count');
        $totalInitiatives = $totalMappedInitiatives + count($unassignedInitiatives);
        $activeCoeCount = (int) $allCards->filter(fn (array $card): bool => ($card['initiatives_count'] ?? 0) > 0)->count();
        $topCard = $allCards->sortByDesc('initiatives_count')->first();
        $technologyTotal = (int) collect($technologyCards)->sum('initiatives_count');

        return [
            'total_initiatives' => $totalInitiatives,
            'mapped_initiatives' => $totalMappedInitiatives,
            'technology_initiatives' => $technologyTotal,
            'active_coe_count' => $activeCoeCount,
            'unassigned_count' => count($unassignedInitiatives),
            'top_coe_name' => $topCard['display_name'] ?? '-',
            'top_coe_count' => (int) ($topCard['initiatives_count'] ?? 0),
        ];
    }

    private function getDualGrowthGoals(): array
    {
        $goals = Goal::query()
            ->select(['id', 'code', 'title'])
            ->where('pilar', '2')
            ->orderBy('code')
            ->get();

        if ($goals->isEmpty()) {
            return self::FALLBACK_DUAL_GROWTH_GOALS;
        }

        return $goals
            ->map(fn (Goal $goal): array => [
                'id' => (int) $goal->id,
                'code' => $goal->code,
                'title' => $goal->title,
            ])
            ->values()
            ->all();
    }

    private function getFocusBands(array $dualGrowthGoals): array
    {
        return collect($dualGrowthGoals)
            ->map(fn (array $goal): array => [
                'id' => is_numeric($goal['id']) ? 'goal-'.$goal['id'] : (string) $goal['id'],
                'code' => $goal['code'],
                'title' => $goal['title'],
                'label' => $goal['title'],
            ])
            ->values()
            ->all();
    }

    private function getRoofSection(): array
    {
        $goals = Goal::query()
            ->with(['themes' => fn ($query) => $query->orderBy('theme_number')])
            ->where('pilar', '2')
            ->whereIn('code', ['A', 'B'])
            ->get()
            ->keyBy('code');

        /** @var Goal|null $mainGoal */
        $mainGoal = $goals->get('A');
        /** @var Goal|null $sideGoal */
        $sideGoal = $goals->get('B');

        return [
            'main_goal' => $mainGoal
                ? [
                    'id' => (int) $mainGoal->id,
                    'code' => $mainGoal->code,
                    'title' => $mainGoal->title,
                ]
                : null,
            'main_goal_themes' => collect($mainGoal?->themes ?? [])
                ->take(2)
                ->map(fn ($theme): array => [
                    'id' => (int) $theme->id,
                    'theme_number' => (int) $theme->theme_number,
                    'name' => $theme->name,
                    'label' => $theme->name,
                ])
                ->values()
                ->all(),
            'side_goal' => $sideGoal
                ? [
                    'id' => (int) $sideGoal->id,
                    'code' => $sideGoal->code,
                    'title' => $sideGoal->title,
                ]
                : null,
        ];
    }

    private function getUnassignedInitiatives(int $initiativeType): array
    {
        return MstInitiative::query()
            ->select(['id', 'code', 'name', 'status'])
            ->with('latestStatus')
            ->where('tipe_initiative', $initiativeType)
            ->whereNull('coe_id')
            ->orderBy('code')
            ->get()
            ->map(function (MstInitiative $initiative): array {
                $status = $this->normalizeStatus($initiative->latestPlanningStatusValue());

                return [
                    'id' => (int) $initiative->id,
                    'code' => $initiative->code,
                    'name' => $initiative->name,
                    'label' => trim(collect([$initiative->code, $initiative->name])->filter()->implode(' - ')),
                    'status' => $status,
                    'status_label' => $this->statusLabel($status),
                ];
            })
            ->values()
            ->all();
    }

    private function buildStatusBreakdown(Collection $initiatives): array
    {
        return collect(['drafting', 'propose', 'review', 'approved', 'other'])
            ->map(function (string $status) use ($initiatives): array {
                return [
                    'key' => $status,
                    'label' => $this->statusLabel($status),
                    'count' => (int) $initiatives->where('status', $status)->count(),
                ];
            })
            ->filter(fn (array $item): bool => $item['count'] > 0)
            ->values()
            ->all();
    }

    private function normalizeStatus(?string $status): string
    {
        $value = Str::lower(trim((string) $status));

        if ($value === '' || in_array($value, ['0', '1', 'draft', 'drafting', 'not start'], true)) {
            return 'drafting';
        }

        if ($value === '2' || Str::contains($value, 'propose')) {
            return 'propose';
        }

        if ($value === '3' || Str::contains($value, 'review')) {
            return 'review';
        }

        if (in_array($value, ['4', 'approve', 'approved', 'aproved'], true)) {
            return 'approved';
        }

        return 'other';
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'drafting' => 'Drafting',
            'propose' => 'Propose',
            'review' => 'Review',
            'approved' => 'Approved',
            default => 'Other',
        };
    }
}

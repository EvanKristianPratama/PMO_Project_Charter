<?php

namespace App\Services\ProgramPlanning\StrategicHouse;

use App\Models\Goal;
use App\Models\InitiativeTagging;
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

    private const DUAL_GROWTH_GOAL_CODES = ['A', 'B'];

    private const FALLBACK_DUAL_GROWTH_GOALS = [
        [
            'id' => 'goal-a',
            'code' => 'A',
            'title' => 'Maximize Legacy Business',
            'themes' => [
                [
                    'id' => 'theme-a1',
                    'theme_number' => 1,
                    'name' => 'Maximizing Value',
                    'label' => 'Maximizing Value',
                    'initiatives_count' => 0,
                    'initiatives' => [],
                ],
                [
                    'id' => 'theme-a2',
                    'theme_number' => 2,
                    'name' => 'Expand to new markets & adjacencies',
                    'label' => 'Expand to new markets & adjacencies',
                    'initiatives_count' => 0,
                    'initiatives' => [],
                ],
            ],
            'initiatives_count' => 0,
            'direct_initiatives_count' => 0,
            'direct_initiatives' => [],
        ],
        [
            'id' => 'goal-b',
            'code' => 'B',
            'title' => 'Building low carbon business',
            'themes' => [],
            'initiatives_count' => 0,
            'direct_initiatives_count' => 0,
            'direct_initiatives' => [],
        ],
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

        $previewInitiatives = $initiatives->take(3)->values();

        return [
            'id' => (int) $coe->id,
            'name' => $coe->name,
            'display_name' => $coe->name,
            'tone' => 'default',
            'initiatives_count' => $initiatives->count(),
            'is_empty' => $initiatives->isEmpty(),
            'initiatives' => $initiatives->all(),
            'initiatives_preview' => $previewInitiatives->all(),
            'remaining_initiatives_count' => max(0, $initiatives->count() - $previewInitiatives->count()),
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
        $directInitiativesByGoal = $this->getDirectDualGrowthInitiatives();

        $goals = Goal::query()
            ->select(['id', 'code', 'title', 'pilar'])
            ->with([
                'themes' => fn ($query) => $query
                    ->select(['id', 'idGoal', 'theme_number', 'name'])
                    ->with([
                        'initiativeTaggings' => fn ($taggingQuery) => $taggingQuery
                            ->select(['id', 'themes_id', 'initiative_id', 'pilar'])
                            ->where('pilar', 2)
                            ->with([
                                'initiative' => fn ($initiativeQuery) => $initiativeQuery
                                    ->select(['id', 'code', 'name', 'coe_id'])
                                    ->with(['coe:id,name']),
                            ]),
                    ])
                    ->orderBy('theme_number'),
            ])
            ->where('pilar', 2)
            ->whereIn('code', self::DUAL_GROWTH_GOAL_CODES)
            ->orderByRaw("case code when 'A' then 1 when 'B' then 2 else 99 end")
            ->get();

        $goalsByCode = $goals->keyBy(fn (Goal $goal): string => strtoupper((string) $goal->code));

        return collect(self::FALLBACK_DUAL_GROWTH_GOALS)
            ->map(function (array $fallbackGoal) use ($goalsByCode, $directInitiativesByGoal): array {
                /** @var Goal|null $goal */
                $goal = $goalsByCode->get($fallbackGoal['code']);
                $directInitiatives = $directInitiativesByGoal->get($fallbackGoal['code'], []);

                if ($goal) {
                    return $this->mapDualGrowthGoal($goal, $directInitiatives);
                }

                return [
                    ...$fallbackGoal,
                    'direct_initiatives_count' => count($directInitiatives),
                    'direct_initiatives' => $directInitiatives,
                    'initiatives_count' => (int) ($fallbackGoal['initiatives_count'] ?? 0) + count($directInitiatives),
                ];
            })
            ->values()
            ->all();
    }

    private function getDirectDualGrowthInitiatives(): Collection
    {
        return InitiativeTagging::query()
            ->select(['id', 'goal', 'initiative_id', 'pilar', 'themes_id'])
            ->with([
                'initiative' => fn ($query) => $query
                    ->select(['id', 'code', 'name', 'coe_id'])
                    ->with(['coe:id,name']),
            ])
            ->where('pilar', 2)
            ->whereIn('goal', self::DUAL_GROWTH_GOAL_CODES)
            ->whereNull('themes_id')
            ->get()
            ->groupBy(fn ($tagging): string => strtoupper((string) $tagging->goal))
            ->map(function (Collection $taggings): array {
                return $taggings
                    ->map(fn ($tagging) => $tagging->initiative)
                    ->filter()
                    ->unique('id')
                    ->sortBy(fn ($initiative) => sprintf('%08s-%s', (string) $initiative->code, (string) $initiative->name))
                    ->values()
                    ->map(fn ($initiative): array => $this->mapDualGrowthInitiative($initiative))
                    ->all();
            });
    }

    private function mapDualGrowthGoal(Goal $goal, array $directInitiatives = []): array
    {
        $themes = collect($goal->themes ?? [])
            ->map(fn ($theme): array => $this->mapDualGrowthTheme($theme))
            ->values()
            ->all();

        return [
            'id' => (int) $goal->id,
            'code' => (string) $goal->code,
            'title' => (string) $goal->title,
            'themes' => $themes,
            'direct_initiatives_count' => count($directInitiatives),
            'direct_initiatives' => $directInitiatives,
            'initiatives_count' => (int) collect($themes)->sum('initiatives_count') + count($directInitiatives),
        ];
    }

    private function mapDualGrowthTheme($theme): array
    {
        $initiatives = collect($theme->initiativeTaggings ?? [])
            ->map(fn ($tagging) => $tagging->initiative)
            ->filter()
            ->unique('id')
            ->sortBy(fn ($initiative) => sprintf('%08s-%s', (string) $initiative->code, (string) $initiative->name))
            ->values()
            ->map(fn ($initiative): array => $this->mapDualGrowthInitiative($initiative))
            ->all();

        return [
            'id' => (int) $theme->id,
            'theme_number' => (int) $theme->theme_number,
            'name' => (string) $theme->name,
            'label' => (string) $theme->name,
            'initiatives_count' => count($initiatives),
            'initiatives' => $initiatives,
        ];
    }

    private function mapDualGrowthInitiative($initiative): array
    {
        return [
            'id' => (int) $initiative->id,
            'code' => $initiative->code,
            'name' => $initiative->name,
            'coe_id' => $initiative->coe_id ? (int) $initiative->coe_id : null,
            'coe_name' => $initiative->coe?->name,
            'label' => trim(collect([$initiative->code, $initiative->name])->filter()->implode(' - ')),
        ];
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

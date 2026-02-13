<?php

namespace App\Http\Controllers\DigitalInitiative;

use App\Http\Controllers\Controller;
use App\Models\DigitalInitiative;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DigitalInitiativeController extends Controller
{
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $type = $request->input('type');

        $initiatives = DigitalInitiative::query()
            ->when($search, fn ($q, $search) => $q->where(function ($inner) use ($search) {
                $inner->where('no', 'like', "%{$search}%")
                    ->orWhere('useCase', 'like', "%{$search}%")
                    ->orWhere('projectOwner', 'like', "%{$search}%")
                    ->orWhere('desc', 'like', "%{$search}%");
            }))
            ->when($type, fn ($q, $type) => $q->where('type', $type))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('DigitalInitiatives/Index', [
            'initiatives' => $initiatives,
            'filters' => [
                'search' => $search,
                'type' => $type,
            ],
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('DigitalInitiatives/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'no' => 'required|string|max:255',
            'projectOwner' => 'nullable|string|max:255',
            'useCase' => 'nullable|string|max:255',
            'desc' => 'nullable|string',
            'value' => 'nullable|string',
            'urgency' => 'nullable|string|max:255',
            'rjjp' => 'nullable|string|max:255',
            'coe' => 'nullable|string|max:255',
        ]);

        DigitalInitiative::create($validated);

        return redirect()->route('digital-initiatives.index')->with('success', 'Digital Initiative created successfully.');
    }

    public function show(DigitalInitiative $digitalInitiative): Response
    {
        return Inertia::render('DigitalInitiatives/Show', [
            'initiative' => $digitalInitiative,
        ]);
    }

    public function edit(DigitalInitiative $digitalInitiative): Response
    {
        return Inertia::render('DigitalInitiatives/Edit', [
            'initiative' => $digitalInitiative,
        ]);
    }

    public function update(Request $request, DigitalInitiative $digitalInitiative): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'no' => 'required|string|max:255',
            'projectOwner' => 'nullable|string|max:255',
            'useCase' => 'nullable|string|max:255',
            'desc' => 'nullable|string',
            'value' => 'nullable|string',
            'urgency' => 'nullable|string|max:255',
            'rjjp' => 'nullable|string|max:255',
            'coe' => 'nullable|string|max:255',
        ]);

        $digitalInitiative->update($validated);

        return redirect()->route('digital-initiatives.index')->with('success', 'Digital Initiative updated successfully.');
    }

    public function destroy(DigitalInitiative $digitalInitiative): RedirectResponse
    {
        $digitalInitiative->delete();

        return redirect()->route('digital-initiatives.index')->with('success', 'Digital Initiative deleted successfully.');
    }
}

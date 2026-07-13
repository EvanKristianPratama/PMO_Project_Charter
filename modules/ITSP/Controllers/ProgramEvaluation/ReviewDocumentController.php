<?php

namespace Modules\ITSP\Controllers\ProgramEvaluation;

use App\Http\Controllers\Controller;
use Modules\ITSP\Services\ProgramEvaluation\ReviewDocument\ItDocumentService;
use Modules\ITSP\Services\ProgramEvaluation\ReviewDocument\DigitalDocumentService;
use Inertia\Inertia;
use Inertia\Response;

class ReviewDocumentController extends Controller
{
    public function index(
        ItDocumentService $itService,
        DigitalDocumentService $digitalService
    ): Response {
        $itInitiatives = $itService->getItInitiatives();
        $digitalInitiatives = $digitalService->getDigitalInitiatives();

        return Inertia::render('modules/ITSP/ProgramEvaluation/ReviewDocument/Index', [
            'it_projects' => $itInitiatives,
            'digital_projects' => $digitalInitiatives,
            // Keep 'projects' for backward compatibility if needed, defaulting to IT
            'projects' => $itInitiatives,
        ]);
    }
}
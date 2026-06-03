<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use PhpOffice\PhpPresentation\IOFactory;
use PhpOffice\PhpPresentation\Writer\HTML;
use Illuminate\Support\Facades\Storage;

class LibaryController extends Controller
{
    public function index()
    {
        // Ensure directory exists
        if (!Storage::disk('public')->exists('ppt')) {
            Storage::disk('public')->makeDirectory('ppt');
        }

        $files = Storage::disk('public')->files('ppt');
        $pptFiles = array_map(function ($file) {
            return [
                'name' => basename($file),
                'path' => $file,
                'url' => Storage::url($file),
            ];
        }, array_filter($files, function ($file) {
            return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['ppt', 'pptx']);
        }));

        return Inertia::render('Libary/Index', [
            'pptFiles' => array_values($pptFiles)
        ]);
    }

    public function show($filename)
    {
        $path = storage_path('app/public/ppt/' . $filename);
        
        if (!file_exists($path)) {
            return redirect()->route('libary.index')->with('error', 'File not found');
        }

        // For now, we return the list with a selected file
        // In a real scenario, we might want to extract slides as images here
        
        $files = Storage::disk('public')->files('ppt');
        $pptFiles = array_map(function ($file) {
            return [
                'name' => basename($file),
                'path' => $file,
                'url' => Storage::url($file),
            ];
        }, array_filter($files, function ($file) {
            return in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['ppt', 'pptx']);
        }));

        return Inertia::render('Libary/Index', [
            'pptFiles' => array_values($pptFiles),
            'selectedPpt' => [
                'name' => $filename,
                'url' => Storage::url('ppt/' . $filename),
                'path' => 'ppt/' . $filename
            ]
        ]);
    }

    /**
     * Optional: Render PPT as HTML using PHPPresentation
     */
    public function viewHtml($filename)
    {
        $path = storage_path('app/public/ppt/' . $filename);
        if (!file_exists($path)) abort(404);

        try {
            $presentation = IOFactory::load($path);
            $writer = new HTML($presentation);
            
            return response()->stream(function() use ($writer) {
                $writer->save('php://output');
            }, 200, ['Content-Type' => 'text/html']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

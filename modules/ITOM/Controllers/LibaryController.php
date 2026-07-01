<?php

namespace Modules\ITOM\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class LibaryController extends Controller
{
    public function index(Request $request)
    {
        // Ensure private directory exists
        if (!Storage::disk('local')->exists('Regulation')) {
            Storage::disk('local')->makeDirectory('Regulation');
        }

        // Initialize table if empty and seed the existing PPTX file
        $this->initializeDemoFiles();

        // Fetch documents
        $documents = Document::where('entity_type', 'regulation')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'uuid' => $doc->uuid,
                    'name' => $doc->original_name,
                    'extension' => $doc->extension,
                    'size' => $doc->size,
                    'mime_type' => $doc->mime_type,
                    'path' => $doc->path,
                    'created_at' => $doc->created_at->toISOString(),
                    'url' => route('itom.libary.document.preview', ['uuid' => $doc->uuid]),
                ];
            });

        return Inertia::render('DMS/Index', [
            'documents' => $documents,
            'selectedDocument' => null,
        ]);
    }

    public function show(Request $request, $uuid)
    {
        // Ensure private directory exists
        if (!Storage::disk('local')->exists('Regulation')) {
            Storage::disk('local')->makeDirectory('Regulation');
        }

        $this->initializeDemoFiles();

        $selectedDoc = Document::where('uuid', $uuid)->firstOrFail();

        $documents = Document::where('entity_type', 'regulation')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'uuid' => $doc->uuid,
                    'name' => $doc->original_name,
                    'extension' => $doc->extension,
                    'size' => $doc->size,
                    'mime_type' => $doc->mime_type,
                    'path' => $doc->path,
                    'created_at' => $doc->created_at->toISOString(),
                    'url' => route('itom.libary.document.preview', ['uuid' => $doc->uuid]),
                ];
            });

        return Inertia::render('DMS/Index', [
            'documents' => $documents,
            'selectedDocument' => [
                'id' => $selectedDoc->id,
                'uuid' => $selectedDoc->uuid,
                'name' => $selectedDoc->original_name,
                'extension' => $selectedDoc->extension,
                'size' => $selectedDoc->size,
                'mime_type' => $selectedDoc->mime_type,
                'path' => $selectedDoc->path,
                'url' => route('itom.libary.document.preview', ['uuid' => $selectedDoc->uuid]),
            ]
        ]);
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,ppt,pptx|max:20480', // max 20MB
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = $file->getMimeType();
        $size = $file->getSize();
        
        $uuid = (string) Str::uuid();
        $storedName = $uuid . '.' . $extension;
        
        // Save to private storage Regulation/
        $path = $file->storeAs('Regulation', $storedName, 'local');

        $document = Document::create([
            'uuid' => $uuid,
            'entity_type' => 'regulation',
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'path' => $path,
            'mime_type' => $mimeType,
            'extension' => $extension,
            'size' => $size,
            'uploaded_by' => auth()->id(),
        ]);

        return redirect()->route('itom.libary.show', ['uuid' => $uuid])
            ->with('success', 'File berhasil diunggah.');
    }

    public function previewFile($uuid)
    {
        $document = Document::where('uuid', $uuid)->firstOrFail();

        if (!Storage::disk('local')->exists($document->path)) {
            abort(404, 'File not found on storage');
        }

        $path = Storage::disk('local')->path($document->path);

        return response()->file($path, [
            'Content-Type' => $document->mime_type,
            'Content-Disposition' => 'inline; filename="' . $document->original_name . '"'
        ]);
    }

    public function downloadFile($uuid)
    {
        $document = Document::where('uuid', $uuid)->firstOrFail();

        if (!Storage::disk('local')->exists($document->path)) {
            abort(404, 'File not found on storage');
        }

        $path = Storage::disk('local')->path($document->path);

        return response()->download($path, $document->original_name);
    }

    public function destroy($uuid)
    {
        $document = Document::where('uuid', $uuid)->firstOrFail();

        // Delete file from storage
        if (Storage::disk('local')->exists($document->path)) {
            Storage::disk('local')->delete($document->path);
        }

        // Delete from database
        $document->delete();

        return redirect()->route('itom.libary.index')
            ->with('success', 'File berhasil dihapus.');
    }

    private function initializeDemoFiles()
    {
        if (Document::where('entity_type', 'regulation')->count() === 0) {
            // Check if public demo PPTX file exists
            if (Storage::disk('public')->exists('ppt/IT_Operating_Model_a776c8ce.pptx')) {
                $uuid = (string) Str::uuid();
                $storedName = $uuid . '.pptx';
                $privatePath = 'Regulation/' . $storedName;
                
                // Copy to private storage
                $content = Storage::disk('public')->get('ppt/IT_Operating_Model_a776c8ce.pptx');
                Storage::disk('local')->put($privatePath, $content);
                
                // Create document entry
                Document::create([
                    'uuid' => $uuid,
                    'entity_type' => 'regulation',
                    'original_name' => 'IT_Operating_Model_a776c8ce.pptx',
                    'stored_name' => $storedName,
                    'path' => $privatePath,
                    'mime_type' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                    'extension' => 'pptx',
                    'size' => strlen($content),
                    'uploaded_by' => null,
                ]);
            }
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Models\Document;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function index(): Response
    {
        $page = $this->inertiaPage(
            Document::query()->with('project')->latest(),
            fn (Document $document) => $document->toInertia()
        );

        return Inertia::render('Reports/Documents', [
            'documents' => $page['data'],
            'pagination' => $page['pagination'],
            'projects' => $this->projectOptions(),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Reports/Documents', [
            'documents' => [],
            'projects' => $this->projectOptions(),
            'formMode' => 'create',
        ]);
    }

    public function store(StoreDocumentRequest $request): RedirectResponse
    {
        $payload = $request->safe()->except('file');

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $payload['path'] = $file->store('documents');
            $payload['disk'] = 'local';
            $payload['size_bytes'] = $file->getSize();
            $payload['mime_type'] = $file->getMimeType();
            $payload['name'] = $payload['name'] ?? $file->getClientOriginalName();
        }

        Document::query()->create($payload);

        return redirect()->route('reports.documents')->with('success', 'Document saved.');
    }

    public function show(Document $document): Response
    {
        $document->load('project');

        return Inertia::render('Reports/Documents', [
            'documents' => [$document->toInertia()],
            'document' => $document->toInertia(),
            'projects' => $this->projectOptions(),
        ]);
    }

    public function edit(Document $document): Response
    {
        $document->load('project');

        return Inertia::render('Reports/Documents', [
            'documents' => [$document->toInertia()],
            'document' => $document->toInertia(),
            'projects' => $this->projectOptions(),
            'formMode' => 'edit',
        ]);
    }

    public function update(UpdateDocumentRequest $request, Document $document): RedirectResponse
    {
        $payload = $request->safe()->except('file');

        if ($request->hasFile('file')) {
            if ($document->path) {
                Storage::disk($document->disk ?: 'local')->delete($document->path);
            }
            $file = $request->file('file');
            $payload['path'] = $file->store('documents');
            $payload['size_bytes'] = $file->getSize();
            $payload['mime_type'] = $file->getMimeType();
        }

        $document->update($payload);

        return redirect()->route('reports.documents')->with('success', 'Document updated.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        if ($document->path) {
            Storage::disk($document->disk ?: 'local')->delete($document->path);
        }

        $document->delete();

        return redirect()->route('reports.documents')->with('success', 'Document removed.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProjectDocumentController extends Controller
{
    /**
     * Allowed roles for document access.
     */
    private array $allowedRoles = ['CEO', 'Project Manager'];

    /**
     * Check if user has access to documents.
     */
    private function checkAccess(): bool
    {
        return auth()->user()->hasAnyRole($this->allowedRoles);
    }

    /**
     * Display documents for a project.
     */
    public function index(Project $project)
    {
        if (!$this->checkAccess()) {
            abort(403, 'Unauthorized access');
        }

        $documents = $project->documents()
            ->with('uploader')
            ->orderBy('category')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('category');

        return view('projects.documents', [
            'project' => $project,
            'documents' => $documents,
            'categories' => ProjectDocument::CATEGORY_LABELS,
        ]);
    }

    /**
     * Upload a new document.
     */
    public function store(Request $request, Project $project)
    {
        if (!$this->checkAccess()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'category' => 'required|in:quotation,spk,bast,srd',
            'file' => [
                'required',
                'file',
                'max:20480',
                function ($attribute, $value, $fail) {
                    $allowedMimes = [
                        'application/pdf',
                        'application/msword', // .doc
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document', // .docx
                        'application/vnd.ms-excel', // .xls
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
                    ];
                    $allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

                    $extension = strtolower($value->getClientOriginalExtension());
                    $mimeType = $value->getMimeType();

                    if (!in_array($extension, $allowedExtensions)) {
                        $fail('The file must be a PDF, Word, or Excel document.');
                    }
                },
            ],
        ]);

        $file = $request->file('file');
        $path = $file->store('project-documents/' . $project->id, 'public');

        $document = $project->documents()->create([
            'category' => $validated['category'],
            'file_path' => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
            'uploaded_by' => auth()->id(),
        ]);

        $document->load('uploader');

        return response()->json([
            'message' => 'Document uploaded successfully',
            'document' => [
                'id' => $document->id,
                'category' => $document->category,
                'category_label' => $document->category_label,
                'file_name' => $document->file_name,
                'file_size_human' => $document->file_size_human,
                'file_icon' => $document->file_icon,
                'uploader' => $document->uploader->name,
                'created_at' => $document->created_at->format('M d, Y H:i'),
                'download_url' => route('projects.documents.download', [$project, $document]),
            ],
        ]);
    }

    /**
     * Download a document.
     */
    public function download(Project $project, ProjectDocument $document)
    {
        if (!$this->checkAccess()) {
            abort(403, 'Unauthorized access');
        }

        if ($document->project_id !== $project->id) {
            abort(404);
        }

        $path = Storage::disk('public')->path($document->file_path);
        return response()->download($path, $document->file_name);
    }

    /**
     * Delete a document.
     */
    public function destroy(Project $project, ProjectDocument $document)
    {
        if (!$this->checkAccess()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($document->project_id !== $project->id) {
            return response()->json(['message' => 'Document not found'], 404);
        }

        // Delete file from storage
        Storage::disk('public')->delete($document->file_path);

        // Delete database record
        $document->delete();

        return response()->json([
            'message' => 'Document deleted successfully',
        ]);
    }
}

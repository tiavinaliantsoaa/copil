<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\AuditLog;
use App\Models\CommunicationReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AttachmentController extends Controller
{
    public function store(Request $request, CommunicationReport $report): RedirectResponse
    {
        $this->authorizeEdit($request, $report);
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:160', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'file' => ['required', 'file', 'max:15360', 'mimes:jpg,jpeg,png,webp,pdf,ppt,pptx,doc,docx,xls,xlsx,csv'],
        ]);

        $file = $validated['file'];
        $path = $file->store("copil/{$report->period->key}/{$validated['category']}", 'local');
        $attachment = $report->attachments()->create([
            'category' => $validated['category'],
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'mime_type' => $file->getMimeType() ?: 'application/octet-stream',
            'size' => $file->getSize(),
            'uploaded_by' => $request->user()->id,
        ]);

        AuditLog::create([
            'user_id' => $request->user()->id, 'action' => 'attachment.uploaded',
            'subject_type' => Attachment::class, 'subject_id' => $attachment->id,
            'context' => ['period' => $report->period->key, 'category' => $attachment->category],
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('copil.index', ['period' => $report->period->key])
            ->withFragment($request->string('_panel', 'dashboard')->toString())
            ->with('success', 'Le fichier a été ajouté.');
    }

    public function show(Request $request, Attachment $attachment): BinaryFileResponse|RedirectResponse
    {
        if (!$request->user() && !$this->isPresentationImage($attachment)) {
            return redirect()->guest(route('login'));
        }

        abort_unless(Storage::disk('local')->exists($attachment->path), 404);
        $path = Storage::disk('local')->path($attachment->path);

        if ($request->boolean('download') || !$attachment->isImage()) {
            return response()->download($path, $attachment->original_name);
        }

        return response()->file($path, ['Content-Type' => $attachment->mime_type, 'Cache-Control' => 'private, max-age=3600']);
    }

    public function destroy(Request $request, Attachment $attachment): RedirectResponse
    {
        $report = $attachment->report;
        $this->authorizeEdit($request, $report);
        Storage::disk('local')->delete($attachment->path);
        $attachment->delete();

        return back()->with('success', 'Le fichier a été supprimé.');
    }

    private function isPresentationImage(Attachment $attachment): bool
    {
        if (!$attachment->isImage()) {
            return false;
        }

        return $attachment->category === 'graphics'
            || (bool) preg_match('/^content-[a-z0-9]+-(best|improvement)$/', $attachment->category);
    }

    private function authorizeEdit(Request $request, CommunicationReport $report): void
    {
        abort_unless($request->user()->canEditReports(), 403);
        abort_if($report->period->isClosed() && !$request->user()->isAdmin(), 423, 'Cette période est clôturée.');
    }
}

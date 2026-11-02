<?php

namespace App\Http\Controllers;

use App\Models\UserDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminDocumentController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status');

        $query = UserDocument::with('user')
            ->latest();

        if (in_array($status, [
            'pending',
            'approved',
            'rejected',
        ], true)) {
            $query->where(
                'verification_status',
                $status
            );
        }

        $documents = $query->paginate(20)
            ->withQueryString();

        $counts = [
            'all' => UserDocument::count(),

            'pending' => UserDocument::where(
                'verification_status',
                'pending'
            )->count(),

            'approved' => UserDocument::where(
                'verification_status',
                'approved'
            )->count(),

            'rejected' => UserDocument::where(
                'verification_status',
                'rejected'
            )->count(),
        ];

        return view('admin.documents.index', [
            'documents' => $documents,
            'counts' => $counts,
            'currentStatus' => $status,
        ]);
    }

    public function show(
        UserDocument $document
    ): View {
        $document->load('user');

        return view('admin.documents.show', [
            'document' => $document,
        ]);
    }

    public function download(
        UserDocument $document
    ) {
        abort_unless(
            $document->storage_path &&
            Storage::disk('local')->exists(
                $document->storage_path
            ),
            404
        );

        return Storage::disk('local')->download(
            $document->storage_path,
            $document->original_name
        );
    }

    public function approve(
        Request $request,
        UserDocument $document
    ): RedirectResponse {
        $document->update([
            'verification_status' => 'approved',
            'admin_notes' => $request->input('admin_notes'),
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return redirect()
            ->route(
                'admin.documents.show',
                $document
            )
            ->with(
                'status',
                'document-approved'
            );
    }

    public function reject(
        Request $request,
        UserDocument $document
    ): RedirectResponse {
        $validated = $request->validate([
            'admin_notes' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $document->update([
            'verification_status' => 'rejected',
            'admin_notes' => $validated['admin_notes'],
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return redirect()
            ->route(
                'admin.documents.show',
                $document
            )
            ->with(
                'status',
                'document-rejected'
            );
    }
}
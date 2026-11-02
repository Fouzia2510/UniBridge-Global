<?php

namespace App\Http\Controllers;

use App\Models\UserDocument;
use Google\Client as GoogleClient;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Http\MediaFileUpload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class DocumentController extends Controller
{
    /**
     * Show the authenticated user's documents.
     */
    public function index(Request $request): View
    {
        $documents = $request->user()
            ->documents()
            ->latest()
            ->get();

        return view('documents.index', [
            'documents' => $documents,
        ]);
    }

    /**
     * Upload a new document.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'document_type' => [
                'required',
                'string',
                Rule::in([
                    'cnic',
                    'passport',
                    'degree',
                    'transcript',
                    'english_certificate',
                    'other',
                ]),
            ],

            'title' => [
                'required',
                'string',
                'max:150',
            ],

            'document' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $user = $request->user();
        $uploadedFile = $request->file('document');

        /*
        |--------------------------------------------------------------------------
        | Store document privately in Laravel
        |--------------------------------------------------------------------------
        */

        $path = $uploadedFile->store(
            'user-documents/' . $user->id,
            'local'
        );

        $document = null;

        try {
            /*
            |--------------------------------------------------------------------------
            | Create database record
            |--------------------------------------------------------------------------
            */

            $document = $user->documents()->create([
                'document_type' => $validated['document_type'],
                'title' => $validated['title'],
                'original_name' => $uploadedFile->getClientOriginalName(),
                'storage_path' => $path,
                'verification_status' => 'pending',
            ]);

            /*
            |--------------------------------------------------------------------------
            | Upload to Google Drive if connected
            |--------------------------------------------------------------------------
            */

            if ($this->hasGoogleDriveConnection($user)) {
                $driveData = $this->uploadToGoogleDrive(
                    $user,
                    $path,
                    $uploadedFile->getClientOriginalName(),
                    $validated['title'],
                    $validated['document_type']
                );

                $document->update([
                    'google_drive_file_id' => $driveData['id'],
                    'google_drive_url' => $driveData['url'],
                ]);
            }

            return redirect()
                ->route('documents.index')
                ->with(
                    'status',
                    $this->hasGoogleDriveConnection($user)
                        ? 'Document uploaded successfully and linked to Google Drive.'
                        : 'Document uploaded successfully. Connect Google Drive to create a Drive backup.'
                );

        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Clean up if Google Drive upload fails
            |--------------------------------------------------------------------------
            */

            if ($document) {
                $document->delete();
            }

            if ($path) {
                Storage::disk('local')->delete($path);
            }

            report($e);

            return redirect()
                ->route('documents.index')
                ->withErrors([
                    'google_drive' =>
                        'The document could not be uploaded to Google Drive. Please reconnect Google Drive and try again.',
                ]);
        }
    }

    /**
     * Replace an existing document.
     */
    public function update(
        Request $request,
        UserDocument $document
    ): RedirectResponse {
        $this->authorizeDocument($request, $document);

        $validated = $request->validate([
            'document_type' => [
                'required',
                'string',
                Rule::in([
                    'cnic',
                    'passport',
                    'degree',
                    'transcript',
                    'english_certificate',
                    'other',
                ]),
            ],

            'title' => [
                'required',
                'string',
                'max:150',
            ],

            'document' => [
                'required',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:5120',
            ],
        ]);

        $user = $request->user();
        $uploadedFile = $request->file('document');

        /*
        |--------------------------------------------------------------------------
        | Save the old Drive ID before replacement
        |--------------------------------------------------------------------------
        */

        $oldDriveFileId = $document->google_drive_file_id;
        $oldStoragePath = $document->storage_path;

        /*
        |--------------------------------------------------------------------------
        | Store replacement privately
        |--------------------------------------------------------------------------
        */

        $newPath = $uploadedFile->store(
            'user-documents/' . $user->id,
            'local'
        );

        try {
            $driveData = null;

            /*
            |--------------------------------------------------------------------------
            | Upload replacement to Google Drive
            |--------------------------------------------------------------------------
            */

            if ($this->hasGoogleDriveConnection($user)) {
                $driveData = $this->uploadToGoogleDrive(
                    $user,
                    $newPath,
                    $uploadedFile->getClientOriginalName(),
                    $validated['title'],
                    $validated['document_type']
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Delete old Google Drive file after new upload succeeds
            |--------------------------------------------------------------------------
            */

            if (
                $driveData &&
                $oldDriveFileId
            ) {
                $this->deleteFromGoogleDrive(
                    $user,
                    $oldDriveFileId
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Delete old private file
            |--------------------------------------------------------------------------
            */

            if ($oldStoragePath) {
                Storage::disk('local')->delete(
                    $oldStoragePath
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Update database record
            |--------------------------------------------------------------------------
            */

            $document->update([
                'document_type' => $validated['document_type'],
                'title' => $validated['title'],
                'original_name' => $uploadedFile->getClientOriginalName(),
                'storage_path' => $newPath,

                'google_drive_file_id' =>
                    $driveData['id'] ?? null,

                'google_drive_url' =>
                    $driveData['url'] ?? null,

                /*
                |--------------------------------------------------------------------------
                | Replacement requires fresh verification
                |--------------------------------------------------------------------------
                */

                'verification_status' => 'pending',
                'admin_notes' => null,
                'verified_by' => null,
                'verified_at' => null,
            ]);

            return redirect()
                ->route('documents.index')
                ->with(
                    'status',
                    $driveData
                        ? 'Document replaced successfully and Google Drive was updated.'
                        : 'Document replaced successfully.'
                );

        } catch (Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | Remove newly uploaded private file if replacement fails
            |--------------------------------------------------------------------------
            */

            Storage::disk('local')->delete($newPath);

            report($e);

            return redirect()
                ->route('documents.index')
                ->withErrors([
                    'google_drive' =>
                        'The replacement document could not be synchronized with Google Drive. Please reconnect Google Drive and try again.',
                ]);
        }
    }

    /**
     * Delete a document.
     */
    public function destroy(
        Request $request,
        UserDocument $document
    ): RedirectResponse {
        $this->authorizeDocument($request, $document);

        $user = $request->user();

        /*
        |--------------------------------------------------------------------------
        | Delete Google Drive copy
        |--------------------------------------------------------------------------
        */

        if ($document->google_drive_file_id) {
            try {
                if ($this->hasGoogleDriveConnection($user)) {
                    $this->deleteFromGoogleDrive(
                        $user,
                        $document->google_drive_file_id
                    );
                }
            } catch (Throwable $e) {
                /*
                |--------------------------------------------------------------------------
                | Do not block local deletion if Drive copy is already gone
                |--------------------------------------------------------------------------
                */

                report($e);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Delete private Laravel file
        |--------------------------------------------------------------------------
        */

        if ($document->storage_path) {
            Storage::disk('local')->delete(
                $document->storage_path
            );
        }

        $document->delete();

        return redirect()
            ->route('documents.index')
            ->with('status', 'document-deleted');
    }

    /**
     * Download/view a private document.
     */
    public function download(
        Request $request,
        UserDocument $document
    ) {
        $this->authorizeDocument($request, $document);

        abort_unless(
            $document->storage_path &&
            Storage::disk('local')->exists($document->storage_path),
            404
        );

        return Storage::disk('local')->download(
            $document->storage_path,
            $document->original_name
        );
    }

    /**
     * Check whether the user has connected Google Drive.
     */
    private function hasGoogleDriveConnection($user): bool
    {
        $token = $user->google_drive_oauth_token;

        return is_array($token)
            && ! empty($token['access_token']);
    }

    /**
     * Upload a private application-created file to Google Drive.
     */
    private function uploadToGoogleDrive(
        $user,
        string $storagePath,
        string $originalName,
        string $title,
        string $documentType
    ): array {
        $drive = $this->googleDriveService($user);

        $fullPath = Storage::disk('local')->path(
            $storagePath
        );

        if (! is_file($fullPath)) {
            throw new \RuntimeException(
                'The private document file could not be found.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | File name used inside Google Drive
        |--------------------------------------------------------------------------
        */

        $driveFileName =
            'UniBridge - ' .
            $documentType .
            ' - ' .
            $title .
            ' - ' .
            $originalName;

        $mimeType = mime_content_type($fullPath)
            ?: 'application/octet-stream';

        /*
        |--------------------------------------------------------------------------
        | Create Google Drive file metadata
        |--------------------------------------------------------------------------
        */

        $fileMetadata = new DriveFile([
            'name' => $driveFileName,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Upload file
        |--------------------------------------------------------------------------
        */

        $chunkSizeBytes = 1024 * 1024;

        $client = $drive->getClient();

        $client->setDefer(true);

        $request = $drive->files->create(
            $fileMetadata,
            [
                'fields' => 'id,name,webViewLink',
            ]
        );

        $media = new MediaFileUpload(
            $client,
            $request,
            $mimeType,
            null,
            true,
            $chunkSizeBytes
        );

        $fileSize = filesize($fullPath);

        $media->setFileSize($fileSize);

        $handle = fopen($fullPath, 'rb');

        if ($handle === false) {
            $client->setDefer(false);

            throw new \RuntimeException(
                'The document file could not be opened.'
            );
        }

        try {
            $status = false;

            while (! $status && ! feof($handle)) {
                $chunk = fread(
                    $handle,
                    $chunkSizeBytes
                );

                if ($chunk === false) {
                    throw new \RuntimeException(
                        'Could not read the document during Google Drive upload.'
                    );
                }

                $status = $media->nextChunk($chunk);
            }
        } finally {
            fclose($handle);
            $client->setDefer(false);
        }

        if (! $status instanceof DriveFile) {
            throw new \RuntimeException(
                'Google Drive did not return a valid uploaded file.'
            );
        }

        return [
            'id' => $status->getId(),

            'url' =>
                $status->getWebViewLink()
                ?: 'https://drive.google.com/file/d/' .
                   $status->getId() .
                   '/view',
        ];
    }

    /**
     * Delete a file created by UniBridge in Google Drive.
     */
    private function deleteFromGoogleDrive(
        $user,
        string $fileId
    ): void {
        $drive = $this->googleDriveService($user);

        try {
            $drive->files->delete($fileId);
        } catch (Throwable $e) {
            /*
            |--------------------------------------------------------------------------
            | If Google says the file is already missing, continue.
            |--------------------------------------------------------------------------
            */

            if (
                ! str_contains(
                    strtolower($e->getMessage()),
                    'not found'
                )
            ) {
                throw $e;
            }
        }
    }

    /**
     * Build authenticated Google Drive service.
     */
    private function googleDriveService($user): Drive
    {
        $token = $user->google_drive_oauth_token;

        if (
            ! is_array($token) ||
            empty($token['access_token'])
        ) {
            throw new \RuntimeException(
                'Google Drive is not connected.'
            );
        }

        $client = new GoogleClient();

        $client->setApplicationName(
            'UniBridge'
        );

        $client->setClientId(
            config('services.google_drive.client_id')
        );

        $client->setClientSecret(
            config('services.google_drive.client_secret')
        );

        $client->setAccessToken($token);

        /*
        |--------------------------------------------------------------------------
        | Refresh expired access token
        |--------------------------------------------------------------------------
        */

        if (
            $client->isAccessTokenExpired() &&
            ! empty($token['refresh_token'])
        ) {
            $newToken = $client->fetchAccessTokenWithRefreshToken(
                $token['refresh_token']
            );

            if (
                ! is_array($newToken) ||
                isset($newToken['error']) ||
                empty($newToken['access_token'])
            ) {
                throw new \RuntimeException(
                    'Google Drive access token could not be refreshed.'
                );
            }

            if (empty($newToken['refresh_token'])) {
                $newToken['refresh_token'] =
                    $token['refresh_token'];
            }

            $user->google_drive_oauth_token = $newToken;
            $user->save();

            $client->setAccessToken($newToken);
        }

        return new Drive($client);
    }

    /**
     * Make sure the logged-in user owns the document.
     */
    private function authorizeDocument(
        Request $request,
        UserDocument $document
    ): void {
        abort_unless(
            $document->user_id === $request->user()->id,
            403
        );
    }
}
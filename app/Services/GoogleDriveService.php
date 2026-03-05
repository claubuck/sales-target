<?php

namespace App\Services;

use Google\Client as GoogleClient;
use Google\Service\Drive;
use Illuminate\Support\Facades\File;

class GoogleDriveService
{
    protected GoogleClient $client;

    protected string $credentialsPath;

    public function __construct(?string $credentialsPath = null)
    {
        $this->credentialsPath = $credentialsPath
            ?? config('services.google_drive.credentials')
            ?? storage_path('app/contarsis-6ef1e04512ed.json');
        $this->client = new GoogleClient;
        $this->client->setAuthConfig($this->credentialsPath);
        $this->client->addScope('https://www.googleapis.com/auth/drive.readonly');
    }

    /** Google Sheets native mime type */
    protected const MIME_TYPE_GOOGLE_SHEETS = 'application/vnd.google-apps.spreadsheet';

    /** Export as Excel for Maatwebsite/Excel */
    protected const MIME_TYPE_XLSX = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';

    /**
     * Download a file from Google Drive (or export a Google Sheet) by ID and return the path to the temporary file.
     * Supports both uploaded files (e.g. xlsx) and native Google Sheets.
     */
    public function downloadFile(string $fileId): string
    {
        $drive = new Drive($this->client);

        $file = $drive->files->get($fileId, ['fields' => 'name,mimeType']);
        $mimeType = $file->getMimeType();
        $name = $file->getName() ?: 'commercial_drive';

        if ($mimeType === self::MIME_TYPE_GOOGLE_SHEETS) {
            $content = $drive->files->export($fileId, self::MIME_TYPE_XLSX, ['alt' => 'media'])->getBody()->getContents();
            $extension = 'xlsx';
        } else {
            $content = $drive->files->get($fileId, ['alt' => 'media'])->getBody()->getContents();
            $extension = pathinfo($name, PATHINFO_EXTENSION) ?: 'xlsx';
            $name = pathinfo($name, PATHINFO_FILENAME);
        }

        $tempDir = storage_path('app/temp');
        if (! File::isDirectory($tempDir)) {
            File::makeDirectory($tempDir, 0755, true);
        }

        $tempPath = $tempDir . '/' . uniqid('commercial_') . '_' . $name . '.' . $extension;
        File::put($tempPath, $content);

        return $tempPath;
    }
}

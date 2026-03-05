<?php

namespace App\Console\Commands;

use App\Imports\CommercialRawImport;
use App\Models\CommercialRaw;
use App\Services\GoogleDriveService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class SyncCommercialFromDriveCommand extends Command
{
    protected $signature = 'commercial:sync-from-drive';

    protected $description = 'Download commercial raw file from Google Drive and import into commercial_raw table.';

    public function handle(GoogleDriveService $drive): int
    {
        $fileId = config('services.google_drive.commercial_file_id');

        if (empty($fileId)) {
            $this->error('GOOGLE_DRIVE_COMMERCIAL_FILE_ID is not set in .env');
            return self::FAILURE;
        }

        $credentialsPath = config('services.google_drive.credentials');
        if (! is_file($credentialsPath)) {
            $this->error('Google Drive credentials file not found: ' . $credentialsPath);
            return self::FAILURE;
        }

        $tempPath = null;

        try {
            $this->info('Downloading file from Google Drive...');
            $tempPath = $drive->downloadFile($fileId);
            $this->info('Downloaded to: ' . $tempPath);

            $this->info('Truncating commercial_raw table...');
            DB::table('commercial_raw')->truncate();

            $this->info('Importing rows...');
            Excel::import(new CommercialRawImport, $tempPath);

            $count = CommercialRaw::count();
            $this->info("Import completed. Total rows in commercial_raw: {$count}");

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error: ' . $e->getMessage());
            if ($this->output->isVerbose()) {
                $this->error($e->getTraceAsString());
            }
            return self::FAILURE;
        } finally {
            if ($tempPath !== null && is_file($tempPath)) {
                @unlink($tempPath);
                $this->line('Temporary file removed.');
            }
        }
    }
}

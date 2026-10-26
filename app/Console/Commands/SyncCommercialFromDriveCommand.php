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

            // El archivo del Drive trae solo los meses recientes: se reemplazan esos meses
            // y se conserva el historial. Todo en una transaccion para no dejar datos a medias.
            $importedAt = now()->startOfSecond();

            $replacedMonths = DB::transaction(function () use ($tempPath, $importedAt) {
                $this->info('Importing rows...');
                Excel::import(new CommercialRawImport($importedAt), $tempPath);

                $months = CommercialRaw::where('imported_at', $importedAt)
                    ->select('ano', 'mes')
                    ->distinct()
                    ->get();

                foreach ($months as $month) {
                    $deleted = CommercialRaw::where('ano', $month->ano)
                        ->whereRaw('LOWER(mes) = ?', [mb_strtolower((string) $month->mes)])
                        ->where(function ($query) use ($importedAt) {
                            $query->whereNull('imported_at')->orWhere('imported_at', '!=', $importedAt);
                        })
                        ->delete();

                    $this->line("  {$month->ano}-{$month->mes}: {$deleted} filas anteriores reemplazadas");
                }

                return $months->count();
            });

            $count = CommercialRaw::count();
            $this->info("Import completed. Months replaced: {$replacedMonths}. Total rows in commercial_raw: {$count}");

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

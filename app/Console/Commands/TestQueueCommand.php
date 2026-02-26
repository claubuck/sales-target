<?php

namespace App\Console\Commands;

use App\Jobs\TestQueueJob;
use Illuminate\Console\Command;

class TestQueueCommand extends Command
{
    protected $signature = 'queue:test';

    protected $description = 'Despacha un job de prueba para verificar que el worker procesa la cola.';

    public function handle(): int
    {
        TestQueueJob::dispatch();

        $this->info('Job de prueba despachado.');
        $this->line('Verifica en unos segundos:');
        $this->line('  - storage/logs/laravel.log (debe aparecer "Queue worker is working!")');
        $this->line('  - O: sudo supervisorctl status (debe mostrar laravel-worker RUNNING)');

        return self::SUCCESS;
    }
}

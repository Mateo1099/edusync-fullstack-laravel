<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class PurgeDemoUsers extends Command
{
    protected $signature = 'users:purge-demo';
    protected $description = 'Elimina usuarios de pruebas con dominio edusync.local';

    public function handle(): int
    {
        $count = User::where('email', 'like', '%@edusync.local')->delete();
        $this->info("Usuarios demo eliminados: {$count}");
        return self::SUCCESS;
    }
}

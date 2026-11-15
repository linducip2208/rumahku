<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Process;

class BackupDatabase extends Command
{
    /**
     * @var string
     */
    protected $signature = 'backup:database {--keep=7 : Jumlah file backup terbaru yang dipertahankan}';

    /**
     * @var string
     */
    protected $description = 'Backup database MySQL via mysqldump ke storage/app/backups (*.sql.gz).';

    public function handle(): int
    {
        $keep = max(1, (int) $this->option('keep'));

        $connection = (string) config('database.default');
        /** @var array<string, mixed> $config */
        $config = (array) config("database.connections.{$connection}");

        $driver = (string) ($config['driver'] ?? '');
        if (! in_array($driver, ['mysql', 'mariadb'], true)) {
            $this->error("Koneksi [{$connection}] driver [{$driver}] tidak didukung. Hanya mysql/mariadb.");

            return self::FAILURE;
        }

        $host = (string) ($config['host'] ?? '127.0.0.1');
        $port = (string) ($config['port'] ?? '3306');
        $database = (string) ($config['database'] ?? '');
        $username = (string) ($config['username'] ?? 'root');
        $password = (string) ($config['password'] ?? '');

        if ($database === '') {
            $this->error('Nama database kosong. Periksa config database.');

            return self::FAILURE;
        }

        $mysqldump = $this->locateMysqldump();
        if ($mysqldump === null) {
            $this->error('mysqldump tidak ditemukan di PATH maupun lokasi Laragon standar.');

            return self::FAILURE;
        }

        $directory = storage_path('app/backups');
        if (! is_dir($directory) && ! mkdir($directory, 0755, true) && ! is_dir($directory)) {
            $this->error("Gagal membuat direktori: {$directory}");

            return self::FAILURE;
        }

        $filename = sprintf('rumah123-%s.sql.gz', date('Ymd-His'));
        $path = $directory.DIRECTORY_SEPARATOR.$filename;

        $command = array_filter([
            $mysqldump,
            '--single-transaction',
            '--routines',
            '--events',
            '--host='.$host,
            '--port='.$port,
            '--user='.$username,
            $password !== '' ? '--password='.$password : null,
            $database,
        ]);

        $result = Process::run($command);

        if (! $result->successful()) {
            $this->error('mysqldump gagal: '.trim($result->errorOutput() ?: $result->output()));

            return self::FAILURE;
        }

        $gzipped = gzencode($result->output(), 9);
        if ($gzipped === false || file_put_contents($path, $gzipped) === false) {
            $this->error("Gagal menulis file backup: {$path}");

            return self::FAILURE;
        }

        $this->pruneOldBackups($directory, $keep);

        $size = filesize($path) ?: 0;
        $this->info(sprintf('Backup selesai: %s (%s)', $path, $this->formatBytes($size)));

        return self::SUCCESS;
    }

    private function locateMysqldump(): ?string
    {
        $explicit = (string) (getenv('MYSQLDUMP_PATH') ?: '');
        if ($explicit !== '' && is_file($explicit)) {
            return $explicit;
        }

        $which = DIRECTORY_SEPARATOR === '\\' ? 'where mysqldump' : 'command -v mysqldump';
        $lookup = Process::run($which);
        if ($lookup->successful()) {
            $first = trim(strtok(trim($lookup->output()), "\r\n"));
            if ($first !== '' && is_file($first)) {
                return $first;
            }
        }

        /** @var list<string> $candidates */
        $candidates = [];
        foreach (['D:\\laragon\\bin\\mysql', 'C:\\laragon\\bin\\mysql'] as $base) {
            if (! is_dir($base)) {
                continue;
            }
            foreach ((array) glob($base.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'mysqldump.exe') as $match) {
                $candidates[] = $match;
            }
        }

        if ($candidates !== []) {
            rsort($candidates);

            return $candidates[0];
        }

        return null;
    }

    private function pruneOldBackups(string $directory, int $keep): void
    {
        /** @var list<string> $files */
        $files = (array) glob($directory.DIRECTORY_SEPARATOR.'rumah123-*.sql.gz');
        sort($files);

        while (count($files) > $keep) {
            $oldest = array_shift($files);
            if (is_string($oldest)) {
                @unlink($oldest);
            }
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes.' B';
        }

        $units = ['KB', 'MB', 'GB'];
        $value = (float) $bytes / 1024;
        foreach ($units as $unit) {
            if ($value < 1024) {
                return sprintf('%.2f %s', $value, $unit);
            }
            $value /= 1024;
        }

        return sprintf('%.2f TB', $value);
    }
}

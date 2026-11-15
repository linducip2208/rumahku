<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Process;
use Tests\TestCase;

class BackupTest extends TestCase
{
    /**
     * @var list<string>
     */
    private array $createdBackups = [];

    protected function tearDown(): void
    {
        foreach ($this->createdBackups as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }

        parent::tearDown();
    }

    public function test_backup_database_creates_gzipped_dump(): void
    {
        if ($this->locateMysqldump() === null) {
            $this->markTestSkipped('mysqldump tidak ditemukan, lewati test backup.');
        }

        $directory = storage_path('app/backups');
        $before = (array) glob($directory.DIRECTORY_SEPARATOR.'rumah123-*.sql.gz');

        $this->artisan('backup:database', ['--keep' => 2])
            ->assertSuccessful();

        $after = (array) glob($directory.DIRECTORY_SEPARATOR.'rumah123-*.sql.gz');
        $fresh = array_values(array_diff($after, $before));

        $this->assertNotEmpty($fresh, 'File backup baru tidak ditemukan di storage/app/backups.');

        foreach ($fresh as $path) {
            $this->createdBackups[] = $path;
            $this->assertFileExists($path);
            $this->assertGreaterThan(0, filesize($path));

            $raw = (string) file_get_contents($path);
            $sql = (string) gzdecode($raw);
            $this->assertStringContainsString('-- MySQL dump', $sql);
        }

        $this->assertLessThanOrEqual(2, count($after));
    }

    private function locateMysqldump(): ?string
    {
        $explicit = (string) (getenv('MYSQLDUMP_PATH') ?: '');
        if ($explicit !== '' && is_file($explicit)) {
            return $explicit;
        }

        $lookup = Process::run(DIRECTORY_SEPARATOR === '\\' ? 'where mysqldump' : 'command -v mysqldump');
        if ($lookup->successful()) {
            $first = trim(strtok(trim($lookup->output()), "\r\n"));
            if ($first !== '' && is_file($first)) {
                return $first;
            }
        }

        foreach (['D:\\laragon\\bin\\mysql', 'C:\\laragon\\bin\\mysql'] as $base) {
            if (! is_dir($base)) {
                continue;
            }
            $matches = (array) glob($base.DIRECTORY_SEPARATOR.'*'.DIRECTORY_SEPARATOR.'bin'.DIRECTORY_SEPARATOR.'mysqldump.exe');
            if ($matches !== []) {
                rsort($matches);

                return $matches[0];
            }
        }

        return null;
    }
}

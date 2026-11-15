<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;

/**
 * Storage settings: read-only status + manual instructions.
 *
 * Keputusan: tidak ada app/Models/Setting.php di repo, sehingga halaman ini
 * TIDAK membuat tabel settings dan TIDAK menulis .env dari UI. Perubahan
 * disk S3 dilakukan manual via environment (lihat instruksi di halaman).
 */
class ManageStorage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-circle-stack';

    protected static ?string $navigationLabel = 'Storage';

    protected static ?string $title = 'Storage Settings';

    protected string $view = 'filament.pages.manage-storage';

    public ?string $testDisk = null;

    public function mount(): void
    {
        $this->testDisk = (string) config('filesystems.default');
    }

    public function content(Schema $schema): Schema
    {
        $default = (string) config('filesystems.default');

        return $schema->components([
            Callout::make('Disk aktif: '.$default)
                ->description('Disk default dari FILESYSTEM_DISK. Perubahan env hanya efektif setelah config:cache dibersihkan saat deploy.')
                ->info(),
            Section::make('Konfigurasi saat ini (read-only)')
                ->description('Nilai di bawah dibaca dari config/filesystems + environment. Tidak dapat diubah dari UI.')
                ->schema([
                    TextInput::make('active_disk')
                        ->label('Disk aktif')
                        ->default($default)
                        ->disabled(),
                    TextInput::make('s3_endpoint')
                        ->label('S3 Endpoint (AWS_ENDPOINT)')
                        ->default((string) config('filesystems.disks.s3.endpoint'))
                        ->disabled(),
                    TextInput::make('s3_bucket')
                        ->label('S3 Bucket (AWS_BUCKET)')
                        ->default((string) config('filesystems.disks.s3.bucket'))
                        ->disabled(),
                    TextInput::make('s3_region')
                        ->label('S3 Region (AWS_DEFAULT_REGION)')
                        ->default((string) config('filesystems.disks.s3.region'))
                        ->disabled(),
                    TextInput::make('s3_key')
                        ->label('S3 Key (AWS_ACCESS_KEY_ID)')
                        ->default((string) config('filesystems.disks.s3.key'))
                        ->disabled(),
                    TextInput::make('s3_secret')
                        ->label('S3 Secret (AWS_SECRET_ACCESS_KEY)')
                        ->default(config('filesystems.disks.s3.secret') ? '•••••••• (terisi)' : '(kosong)')
                        ->disabled(),
                ])
                ->columns(2),
            Section::make('Uji tulis')
                ->description('Menulis file storage-test.txt ke disk terpilih.')
                ->schema([
                    Select::make('testDisk')
                        ->label('Disk untuk uji tulis')
                        ->options([
                            'public' => 'public',
                            's3' => 's3',
                        ])
                        ->required(),
                ]),
            Callout::make('Cara mengganti ke S3 (manual)')
                ->description('1) Isi AWS_ACCESS_KEY_ID, AWS_SECRET_ACCESS_KEY, AWS_DEFAULT_REGION, AWS_BUCKET, dan AWS_ENDPOINT di .env. 2) Set FILESYSTEM_DISK=s3. 3) Jalankan php artisan config:clear (lokal) / config:cache ulang (deploy). UI ini sengaja tidak menulis .env.')
                ->warning(),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('testWrite')
                ->label('Test Write')
                ->icon('heroicon-o-check-circle')
                ->action(function (): void {
                    $disk = $this->testDisk ?: (string) config('filesystems.default');

                    try {
                        Storage::disk($disk)->put('storage-test.txt', 'ok '.now()->toDateTimeString());

                        Notification::make()
                            ->title('Uji tulis berhasil ke disk "'.$disk.'"')
                            ->success()
                            ->send();
                    } catch (\Throwable $e) {
                        Notification::make()
                            ->title('Uji tulis gagal: '.$e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}

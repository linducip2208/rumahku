<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Pengaturan payment gateway. Semua kredensial diisi di admin, bukan di .env.
 *
 * Halaman ini otomatis terdaftar via discoverPages() di AdminPanelProvider,
 * jadi tidak perlu append manual ke provider.
 */
class ManagePayments extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?string $navigationLabel = 'Pembayaran';

    protected static ?string $title = 'Pengaturan Pembayaran';

    protected static ?string $slug = 'pengaturan-pembayaran';

    protected string $view = 'filament.pages.manage-payments';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'default_driver' => Setting::get('payments.default_driver', 'manual'),
            'midtrans_server_key' => Setting::get('payments.midtrans_server_key', ''),
            'midtrans_client_key' => Setting::get('payments.midtrans_client_key', ''),
            'midtrans_is_production' => Setting::getBool('payments.midtrans_is_production', false),
            'xendit_secret_key' => Setting::get('payments.xendit_secret_key', ''),
            'xendit_callback_token' => Setting::get('payments.xendit_callback_token', ''),
            'bank_accounts' => Setting::get('payments.bank_accounts', ''),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Gateway aktif')
                    ->schema([
                        Select::make('default_driver')
                            ->label('Gateway default')
                            ->options([
                                'manual' => 'Manual (transfer bank)',
                                'midtrans' => 'Midtrans (Snap)',
                                'xendit' => 'Xendit (Invoice)',
                            ])
                            ->required()
                            ->helperText('Driver yang dipakai saat user membayar. Isi kredensial driver terkait di bawah.'),
                    ]),
                Section::make('Midtrans')
                    ->schema([
                        TextInput::make('midtrans_server_key')
                            ->label('Server key')
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get): bool => $get('default_driver') === 'midtrans')
                            ->helperText('Wajib bila gateway default = Midtrans. Kosongkan = Midtrans nonaktif.'),
                        TextInput::make('midtrans_client_key')
                            ->label('Client key')
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get): bool => $get('default_driver') === 'midtrans')
                            ->helperText('Untuk Snap.js di frontend.'),
                        Toggle::make('midtrans_is_production')
                            ->label('Mode production')
                            ->helperText('Mati = sandbox. Nyalakan hanya dengan kunci production.'),
                    ]),
                Section::make('Xendit')
                    ->schema([
                        TextInput::make('xendit_secret_key')
                            ->label('Secret key')
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get): bool => $get('default_driver') === 'xendit')
                            ->helperText('Wajib bila gateway default = Xendit.'),
                        TextInput::make('xendit_callback_token')
                            ->label('Callback token')
                            ->password()
                            ->revealable()
                            ->required(fn (Get $get): bool => $get('default_driver') === 'xendit')
                            ->helperText('Wajib di production untuk verifikasi webhook.'),
                    ]),
                Section::make('Transfer manual')
                    ->schema([
                        Textarea::make('bank_accounts')
                            ->label('Daftar rekening bank')
                            ->rows(4)
                            ->required(fn (Get $get): bool => $get('default_driver') === 'manual')
                            ->placeholder("Contoh:\nBCA 1234567890 a.n. PT Rumah123\nMandiri 0987654321 a.n. PT Rumah123")
                            ->helperText('Ditampilkan di halaman instruksi pembayaran manual.')
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Setting::set('payments.default_driver', $data['default_driver'] ?? 'manual', 'payments');
        Setting::set('payments.midtrans_server_key', $data['midtrans_server_key'] ?? '', 'payments');
        Setting::set('payments.midtrans_client_key', $data['midtrans_client_key'] ?? '', 'payments');
        Setting::set('payments.midtrans_is_production', ! empty($data['midtrans_is_production']) ? '1' : '0', 'payments');
        Setting::set('payments.xendit_secret_key', $data['xendit_secret_key'] ?? '', 'payments');
        Setting::set('payments.xendit_callback_token', $data['xendit_callback_token'] ?? '', 'payments');
        Setting::set('payments.bank_accounts', $data['bank_accounts'] ?? '', 'payments');

        Notification::make()
            ->title('Pengaturan pembayaran disimpan.')
            ->success()
            ->send();
    }
}

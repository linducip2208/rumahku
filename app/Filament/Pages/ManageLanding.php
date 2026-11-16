<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

/**
 * Pengaturan landing page. Tersimpan di tabel settings dengan group "landing".
 *
 * Halaman ini otomatis terdaftar via discoverPages() di AdminPanelProvider,
 * jadi tidak perlu append manual ke provider.
 */
class ManageLanding extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?string $navigationLabel = 'Landing Page';

    protected static ?string $title = 'Pengaturan Landing Page';

    protected static ?string $slug = 'pengaturan-landing';

    protected string $view = 'filament.pages.manage-landing';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'hero_title' => Setting::get('landing.hero_title', 'Temukan Rumah Impianmu dengan Mudah'),
            'hero_subtitle' => Setting::get('landing.hero_subtitle', 'Jelajahi properti dijual & disewa di seluruh Indonesia.'),
            'hero_image' => Setting::get('landing.hero_image', 'landing/hero.jpg'),
            'show_trust' => Setting::getBool('landing.show_trust', true),
            'show_featured' => Setting::getBool('landing.show_featured', true),
            'show_categories' => Setting::getBool('landing.show_categories', true),
            'show_collections' => Setting::getBool('landing.show_collections', true),
            'show_cities' => Setting::getBool('landing.show_cities', true),
            'show_latest' => Setting::getBool('landing.show_latest', true),
            'show_agents' => Setting::getBool('landing.show_agents', true),
            'show_seller' => Setting::getBool('landing.show_seller', true),
            'show_articles' => Setting::getBool('landing.show_articles', true),
            'footer_about' => Setting::get('landing.footer_about', ''),
            'social_facebook' => Setting::get('landing.social_facebook', ''),
            'social_instagram' => Setting::get('landing.social_instagram', ''),
            'social_youtube' => Setting::get('landing.social_youtube', ''),
            'social_tiktok' => Setting::get('landing.social_tiktok', ''),
            'contact_phone' => Setting::get('landing.contact_phone', ''),
            'contact_email' => Setting::get('landing.contact_email', ''),
            'contact_address' => Setting::get('landing.contact_address', ''),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Hero')
                    ->schema([
                        TextInput::make('hero_title')
                            ->label('Judul hero')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Textarea::make('hero_subtitle')
                            ->label('Subjudul hero')
                            ->rows(2)
                            ->columnSpanFull(),
                        Select::make('hero_image')
                            ->label('Gambar hero')
                            ->options([
                                'landing/hero.jpg' => 'Hero utama (default)',
                                'landing/hero-2.jpg' => 'Hero alternatif 2',
                                'landing/hero-3.jpg' => 'Hero alternatif 3',
                                'landing/hero-city.jpg' => 'Hero perkotaan',
                            ])
                            ->required()
                            ->helperText('Berkas gambar diletakkan di folder public/landing.'),
                    ]),
                Section::make('Tampilan section')
                    ->description('Aktif/nonaktifkan blok yang tampil di beranda.')
                    ->schema([
                        Toggle::make('show_trust')->label('Blok kepercayaan'),
                        Toggle::make('show_featured')->label('Properti unggulan'),
                        Toggle::make('show_categories')->label('Kategori'),
                        Toggle::make('show_collections')->label('Koleksi'),
                        Toggle::make('show_cities')->label('Kota populer'),
                        Toggle::make('show_latest')->label('Listing terbaru'),
                        Toggle::make('show_agents')->label('Agen'),
                        Toggle::make('show_seller')->label('Blok penjual'),
                        Toggle::make('show_articles')->label('Artikel'),
                    ])
                    ->columns(3),
                Section::make('Footer & kontak')
                    ->schema([
                        Textarea::make('footer_about')
                            ->label('Tentang singkat (footer)')
                            ->rows(3)
                            ->columnSpanFull(),
                        TextInput::make('social_facebook')->label('Facebook')->url()->maxLength(255),
                        TextInput::make('social_instagram')->label('Instagram')->url()->maxLength(255),
                        TextInput::make('social_youtube')->label('YouTube')->url()->maxLength(255),
                        TextInput::make('social_tiktok')->label('TikTok')->url()->maxLength(255),
                        TextInput::make('contact_phone')->label('Telepon kontak')->maxLength(30),
                        TextInput::make('contact_email')->label('Email kontak')->email()->maxLength(255),
                        Textarea::make('contact_address')->label('Alamat kontak')->rows(2)->columnSpanFull(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $textKeys = [
            'hero_title',
            'hero_subtitle',
            'hero_image',
            'footer_about',
            'social_facebook',
            'social_instagram',
            'social_youtube',
            'social_tiktok',
            'contact_phone',
            'contact_email',
            'contact_address',
        ];

        foreach ($textKeys as $key) {
            Setting::set('landing.'.$key, $data[$key] ?? '', 'landing');
        }

        $toggleKeys = [
            'show_trust',
            'show_featured',
            'show_categories',
            'show_collections',
            'show_cities',
            'show_latest',
            'show_agents',
            'show_seller',
            'show_articles',
        ];

        foreach ($toggleKeys as $key) {
            Setting::set('landing.'.$key, ! empty($data[$key]) ? '1' : '0', 'landing');
        }

        Notification::make()
            ->title('Pengaturan landing page disimpan.')
            ->success()
            ->send();
    }
}

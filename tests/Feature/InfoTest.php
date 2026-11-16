<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InfoTest extends TestCase
{
    use RefreshDatabase;

    public function test_info_pages_render(): void
    {
        $pages = [
            '/tentang' => 'Tentang Kami',
            '/bantuan' => 'Pusat Bantuan',
            '/privasi' => 'Kebijakan Privasi',
            '/syarat' => 'Syarat &amp; Ketentuan',
        ];

        foreach ($pages as $url => $title) {
            $this->get($url)
                ->assertStatus(200)
                ->assertSee($title, false)
                ->assertSee('Beranda', false);
        }
    }

    public function test_unknown_info_slug_returns_404(): void
    {
        $this->get('/syarat-ketentuan-tidak-ada-xyz')->assertStatus(404);
    }
}

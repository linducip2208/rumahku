<?php

namespace Tests\Feature;

use App\Models\ContactMessage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_page_renders_form(): void
    {
        $this->get(route('contact'))
            ->assertStatus(200)
            ->assertSee('Hubungi Kami', false);
    }

    public function test_valid_contact_message_is_stored(): void
    {
        $response = $this->post(route('contact.store'), [
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'email' => 'budi@example.com',
            'subject' => 'Tanya properti',
            'message' => 'Apakah unit di Depok masih tersedia?',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Budi Santoso',
            'phone' => '081234567890',
            'message' => 'Apakah unit di Depok masih tersedia?',
            'is_read' => false,
        ]);
        $this->assertFalse(ContactMessage::firstWhere('name', 'Budi Santoso')->is_read);
    }

    public function test_invalid_contact_message_fails_validation(): void
    {
        $response = $this->from(route('contact'))->post(route('contact.store'), [
            'name' => '',
            'phone' => '',
            'message' => '',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors(['name', 'phone', 'message']);
        $this->assertDatabaseCount('contact_messages', 0);
    }
}

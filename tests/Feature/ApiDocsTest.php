<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class ApiDocsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Scramble's RestrictedDocsAccess middleware only allows `local`
        // env by default; authorize docs in `testing` env via gate.
        // NOTE: the callback needs an explicit nullable user parameter,
        // otherwise guest (unauthenticated) checks are denied by Gate.
        Gate::define('viewApiDocs', static fn (?object $user): bool => true);
    }

    public function test_api_docs_ui_returns_200(): void
    {
        $response = $this->get('/docs/api');

        $response->assertStatus(200);
    }

    public function test_api_docs_json_returns_200_with_schema_properties(): void
    {
        $response = $this->get('/docs/api.json');

        $response->assertStatus(200);

        $this->assertStringContainsString('properties', $response->getContent());
    }

    public function test_api_docs_json_covers_versioned_api_routes(): void
    {
        $response = $this->get('/docs/api.json');

        $response->assertStatus(200);

        // Scramble's default single-api-path config strips the `/api`
        // prefix into the server URL, so versioned routes appear as `/v1/*`.
        $paths = $response->json('paths');

        $this->assertIsArray($paths);
        $this->assertArrayHasKey('/v1/properties', $paths);
        $this->assertArrayHasKey('/v1/auth/register', $paths);
    }
}

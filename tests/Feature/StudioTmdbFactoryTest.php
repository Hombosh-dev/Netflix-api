<?php

namespace Tests\Feature;

use App\Models\Studio;
use Database\Factories\StudioTmdbFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class StudioTmdbFactoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Mock HTTP responses for TMDB API
        Http::fake([
            'api.themoviedb.org/3/search/company*' => Http::response([
                'results' => [
                    [
                        'id' => 123,
                        'name' => 'Test Studio',
                        'logo_path' => '/path/to/logo.png',
                        'description' => 'This is a test studio description',
                    ]
                ]
            ], 200),
            'api.themoviedb.org/3/company/123*' => Http::response([
                'id' => 123,
                'name' => 'Test Studio',
                'logo_path' => '/path/to/logo.png',
                'description' => 'This is a test studio description',
                'alternative_names' => ['Test Studio Alt', 'Another Name'],
            ], 200),
            'image.tmdb.org/t/p/*' => Http::response(
                file_get_contents(base_path('tests/fixtures/test-image.jpg')),
                200,
                ['Content-Type' => 'image/jpeg']
            ),
        ]);
    }

    /** @test */
    public function it_creates_studio_from_tmdb_by_name()
    {
        // Create a studio using the factory
        $studio = (new StudioTmdbFactory())->fromTmdbByName('Test Studio')->create();

        // Assert the studio was created with the correct data
        $this->assertInstanceOf(Studio::class, $studio);
        $this->assertEquals('Test Studio', $studio->name);
        $this->assertEquals('This is a test studio description', $studio->description);
        $this->assertNotNull($studio->api_sources);
        $this->assertEquals('tmdb', $studio->api_sources[0]['source']);
        $this->assertEquals('123', $studio->api_sources[0]['id']);
    }

    /** @test */
    public function it_creates_studio_from_tmdb_by_id()
    {
        // Create a studio using the factory
        $studio = (new StudioTmdbFactory())->fromTmdbById(123)->create();

        // Assert the studio was created with the correct data
        $this->assertInstanceOf(Studio::class, $studio);
        $this->assertEquals('Test Studio', $studio->name);
        $this->assertEquals('This is a test studio description', $studio->description);
        $this->assertNotNull($studio->api_sources);
        $this->assertEquals('tmdb', $studio->api_sources[0]['source']);
        $this->assertEquals('123', $studio->api_sources[0]['id']);
        $this->assertEquals(['Test Studio Alt', 'Another Name'], $studio->aliases->toArray());
    }
}

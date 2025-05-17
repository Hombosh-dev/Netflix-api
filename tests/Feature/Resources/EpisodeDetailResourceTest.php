<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\EpisodeDetailResource;
use App\Models\Episode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EpisodeDetailResourceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_includes_default_video_url()
    {
        // Створюємо епізод
        $episode = new Episode();

        // Створюємо ресурс
        $resource = new EpisodeDetailResource($episode);

        // Перетворюємо ресурс в масив
        $array = $resource->toArray(request());

        // Перевіряємо, що масив містить поле default_video_url
        $this->assertArrayHasKey('default_video_url', $array);
        $this->assertStringContainsString('storage/videos/example.mp4', $array['default_video_url']);
    }
}

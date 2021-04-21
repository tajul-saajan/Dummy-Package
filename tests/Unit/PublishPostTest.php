<?php

namespace Tajul\Saajan\Tests\Unit;

use Illuminate\Support\Facades\Bus;
use Tajul\Saajan\Jobs\PublishPost;
use Tajul\Saajan\Models\Post;
use Tajul\Saajan\Tests\TestCase;

class PublishPostTest extends TestCase
{
    /** @test */
    public function it_publishes_a_post()
    {
        Bus::fake();

        $post = Post::factory()->create();

        $this->assertNull($post->published_at);

        PublishPost::dispatch($post);

        Bus::assertDispatched(PublishPost::class, function ($job) use ($post) {
            return $job->post->id === $post->id;
        });
    }
}

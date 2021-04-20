<?php

namespace Tajul\Saajan\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tajul\Saajan\Events\PostWasCreated;
use Tajul\Saajan\Listeners\UpdatePostTitle;
use Tajul\Saajan\Models\Post;
use Tajul\Saajan\Tests\TestCase;
use Tajul\Saajan\Tests\User;

class CreatePostTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    function authenticated_users_can_create_a_post()
    {
        // To make sure we don't start with a Post
        $this->assertCount(0, Post::all());

        Event::fake();

        $author = User::factory()->create();

        $response = $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'My first fake title',
            'body'  => 'My first fake body',
        ]);

        $this->assertCount(1, Post::all());

        tap(Post::first(), function ($post) use ($response, $author) {
            $this->assertEquals('My first fake title', $post->title);
            $this->assertEquals('My first fake body', $post->body);
            $this->assertTrue($post->author->is($author));
            $response->assertRedirect(route('posts.show', $post));
        });
    }

    /** @test */
    function a_post_requires_a_title_and_a_body()
    {
        $author = User::factory()->create();

        $this->actingAs($author)->post(route('posts.store'), [
            'title' => '',
            'body'  => 'Some valid body',
        ])->assertSessionHasErrors('title');

        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'Some valid title',
            'body'  => '',
        ])->assertSessionHasErrors('body');
    }

    /** @test */
    function guests_can_not_create_posts()
    {
        // We're starting from an unauthenticated state
        $this->assertFalse(auth()->check());

        $this->post(route('posts.store'), [
            'title' => 'A valid title',
            'body'  => 'A valid body',
        ])->assertForbidden();
    }

    /** @test */
    function all_posts_are_shown_via_the_index_route()
    {
        // Given we have a couple of Posts
        Post::factory()->create([
            'title' => 'Post number 1'
        ]);
        Post::factory()->create([
            'title' => 'Post number 2'
        ]);
        Post::factory()->create([
            'title' => 'Post number 3'
        ]);

        // $this->get(route('posts.index'))->assertStatus(200);
        // $this->get('/baal')->assertStatus(200);

        // We expect them to all show up
        // with their title on the index route
        $this->get(route('posts.index'))
            ->assertSee('Post number 1', $escaped = true)
            ->assertSee('Post number 2', $escaped = true)
            ->assertSee('Post number 3', $escaped = true)
            ->assertDontSee('Post number 4', $escaped = true);
    }

    /** @test */
    function a_single_post_is_shown_via_the_show_route()
    {
        $post = Post::factory()->create([
            'title' => 'The single post title',
            'body'  => 'The single post body',
        ]);

        $this->get(route('posts.show', $post))
            ->assertSee('The single post title')
            ->assertSee('The single post body');
    }

    /** @test */
    function an_event_is_emitted_when_a_new_post_is_created()
    {
        Event::fake();

        $author = User::factory()->create();

        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'A valid title',
            'body' => 'A valid body',
        ]);

        $post = Post::first();

        Event::assertDispatched(PostWasCreated::class, function ($event) use ($post) {
            return $event->post->id === $post->id;
        });
    }

    /** @test */
    function a_newly_created_posts_title_will_be_changed()
    {
        $post = Post::factory()->create([
            'title' => 'Initial title',
        ]);

        $this->assertEquals('Initial title', $post->title);

        (new UpdatePostTitle())->handle(
            new PostWasCreated($post)
        );

        // $this->assertEquals('New: ' . 'Initial title', $post->fresh()->title);
        $this->assertEquals('New: ' . 'Initial title', $post->title);
    }

    /** @test */
    function the_title_of_a_post_is_updated_whenever_a_post_is_created()
    {
        $author = User::factory()->create();

        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'A valid title',
            'body' => 'A valid body',
        ]);

        $post = Post::first();

        $this->assertEquals('New: ' . 'A valid title', $post->title);
    }

    /** @test */
    function creating_a_post_will_capitalize_the_title()
    {
        $author = User::factory()->create();

        $this->actingAs($author)->post(route('posts.store'), [
            'title' => 'some title that was not capitalized',
            'body' => 'A valid body',
        ]);

        $post = Post::first();

        // 'New: ' was added by our event listener
        $this->assertEquals('New: Some title that was not capitalized', $post->title);
    }
}

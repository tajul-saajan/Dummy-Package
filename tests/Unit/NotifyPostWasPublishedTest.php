<?php

namespace Tajul\Saajan\Tests\Unit;

use Illuminate\Support\Facades\Notification;
use Tajul\Saajan\Models\Post;
use Tajul\Saajan\Notifications\PostWasPublishedNotification;
use Tajul\Saajan\Tests\TestCase;
use Tajul\Saajan\Tests\User;

class NotifyPostWasPublishedTest extends TestCase
{
    /** @test */
    public function it_can_notify_a_user_that_a_post_was_published()
    {
        Notification::fake();

        $post = Post::factory()->create();

        // the User model has the 'Notifiable' trait
        $user = User::factory()->create();

        Notification::assertNothingSent();

        $user->notify(new PostWasPublishedNotification($post));

        Notification::assertSentTo(
            $user,
            PostWasPublishedNotification::class,
            function ($notification) use ($post) {
                return $notification->post->id === $post->id;
            }
        );
    }
}
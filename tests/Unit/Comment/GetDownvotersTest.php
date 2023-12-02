<?php

namespace Tests\Unit\Comment;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetDownvotersTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_downvoters_successfully()
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create();

        $user->votes()->attach($comment, ['direction' => -1]);

        $response = $this->actingAs($user)->getJson("/api/comment/{$comment->id}/downvoters");

        $response->assertStatus(200);
    }

    public function test_get_downvoters_for_non_existing_comment()
    {
        $user = User::factory()->create();
        $nonExistingCommentId = 123456; 

        $response = $this->actingAs($user)->getJson("/api/comment/{$nonExistingCommentId}/downvoters");

        $response->assertStatus(404);
    }
}

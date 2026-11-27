<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

class PostContentLimitTest extends TestCase
{
    use RefreshDatabase;

    public function test_140字の本文は保存される(): void
    {
        $post = $this->createPost();
        $content = str_repeat('あ', 140);

        $this->updateContent($post, $content)
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('posts.index'));

        $this->assertSame($content, $post->fresh()->content);
    }

    public function test_141字の本文はエラーになり保存されない(): void
    {
        $post = $this->createPost();

        $this->updateContent($post, str_repeat('あ', 141))
            ->assertRedirect(route('posts.edit', $post))
            ->assertSessionHasErrors(['content' => '本文を140文字以内で入力してください']);

        $this->assertSame('元の本文', $post->fresh()->content);
    }

    public function test_改行は1字として数える(): void
    {
        $post = $this->createPost();
        $line = str_repeat('あ', 69);

        // 69字 + 改行 + 70字 = 140字（ブラウザと同じく改行は CRLF で送る）
        $this->updateContent($post, $line."\r\n".$line.'あ')
            ->assertSessionHasNoErrors();

        $this->assertSame($line."\n".$line.'あ', $post->fresh()->content);
    }

    private function createPost(): Post
    {
        return Post::create([
            'user_id' => User::factory()->create()->id,
            'category_id' => Category::create(['name' => '雑記'])->id,
            'title' => 'タイトル',
            'content' => '元の本文',
        ]);
    }

    private function updateContent(Post $post, string $content): TestResponse
    {
        return $this->actingAs($post->user)
            ->from(route('posts.edit', $post))
            ->put(route('posts.update', $post), [
                'title' => $post->title,
                'category_id' => $post->category_id,
                'content' => $content,
            ]);
    }
}

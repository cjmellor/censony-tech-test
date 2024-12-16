<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('edit page shows current post data', function () {
    // First, we'll create a post with known data
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create();

    // When we visit the edit page, we should see all the current data
    actingAs($user)
        ->get(route('posts.edit', $post))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Posts/Edit')
            ->where('post.title', $post->title)
            ->where('post.content', $post->content)
            ->where('post.category_id', $post->category_id)
            // We should also have access to all categories for the dropdown
            ->has('categories')
        );
});

test('posts maintain their original publication date when updated', function () {
    // This test ensures we don't accidentally modify the publication date during updates
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create();

    $originalDate = $post->published_at;

    actingAs($user)
        ->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'category_id' => $category->id,
        ]);

    expect($post->fresh())
        ->published_at->toDateTimeString()->toBe($originalDate->toDateTimeString());
});

test('validation rules are enforced during updates', function ($field, $value, $error) {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create();

    actingAs($user)
        ->put(route('posts.update', $post), [
            'title' => $field === 'title' ? $value : $post->title,
            'content' => $field === 'content' ? $value : $post->content,
            'category_id' => $field === 'category_id' ? $value : $post->category_id,
        ])
        ->assertInvalid([$field => $error]);

    // Verify the post wasn't changed
    expect($post->fresh())
        ->title->toBe($post->title)
        ->content->toBe($post->content)
        ->category_id->toBe($post->category_id);
})->with([
    'title is required' => ['title', '', 'The title field is required.'],
    'title max length' => ['title', str_repeat('a', 256), 'The title field must not be greater than 255 characters.'],
    'content is required' => ['content', '', 'The content field is required.'],
    'category is required' => ['category_id', '', 'The category id field is required.'],
    'category must exist' => ['category_id', 99999, 'The selected category id is invalid.'],
]);

test('successful update redirects with success message', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $newCategory = Category::factory()->create();
    $post = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create();

    actingAs($user)
        ->put(route('posts.update', $post), [
            'title' => 'Brand New Title',
            'content' => 'Updated content here',
            'category_id' => $newCategory->id,
        ])
        ->assertRedirect()
        ->assertSessionHas('success', 'Post updated successfully.');

    expect($post->fresh())
        ->title->toBe('Brand New Title')
        ->content->toBe('Updated content here')
        ->category_id->toBe($newCategory->id);
});

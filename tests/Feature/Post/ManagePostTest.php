<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('users can view their own posts', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create();

    actingAs($user)
        ->get(route('posts.show', $post))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Posts/Show')
            ->where('post.id', $post->id)
            ->where('can.edit', true)
            ->where('can.delete', true)
        );
});

test('users can view posts by others', function () {
    $author = User::factory()->create();
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()
        ->for($author, 'author')
        ->for($category)
        ->create();

    actingAs($user)
        ->get(route('posts.show', $post))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Posts/Show')
            ->where('post.id', $post->id)
            ->where('can.edit', false)
            ->where('can.delete', false)
        );
});

test('users can edit their own posts', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $newCategory = Category::factory()->create();
    $post = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create();

    // Test accessing edit page
    actingAs($user)
        ->get(route('posts.edit', $post))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Posts/Edit')
            ->has('post')
            ->has('categories')
        );

    // Test updating post
    actingAs($user)
        ->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'category_id' => $newCategory->id,
        ])
        ->assertRedirect(route('posts.index'))
        ->assertSessionHas('success', 'Post updated successfully.');

    $this->assertDatabaseHas('posts', [
        'id' => $post->id,
        'title' => 'Updated Title',
        'content' => 'Updated Content',
        'category_id' => $newCategory->id,
    ]);
});

test('users cannot edit posts by others', function () {
    $author = User::factory()->create();
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()
        ->for($author, 'author')
        ->for($category)
        ->create();

    // Test accessing edit page
    actingAs($user)
        ->get(route('posts.edit', $post))
        ->assertForbidden();

    // Test updating post
    actingAs($user)
        ->put(route('posts.update', $post), [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
            'category_id' => $category->id,
        ])
        ->assertForbidden();
});

test('users can delete their own posts', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create();

    actingAs($user)
        ->delete(route('posts.destroy', $post))
        ->assertRedirect(route('posts.index'))
        ->assertSessionHas('success', 'Post deleted successfully.');

    $this->assertDatabaseMissing('posts', ['id' => $post->id]);
});

test('users cannot delete posts by others', function () {
    $author = User::factory()->create();
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()
        ->for($author, 'author')
        ->for($category)
        ->create();

    actingAs($user)
        ->delete(route('posts.destroy', $post))
        ->assertForbidden();

    $this->assertDatabaseHas('posts', ['id' => $post->id]);
});

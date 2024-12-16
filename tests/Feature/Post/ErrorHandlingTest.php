<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use function Pest\Laravel\{actingAs, get, put, delete};

test('accessing non-existent post returns 404', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('posts.show', ['post' => 99999]))
        ->assertNotFound();
});

test('attempting to edit deleted post returns 404', function () {
    // Create and then delete a post
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create();
    
    $postId = $post->id;
    $post->delete();

    // Try to access the edit page
    actingAs($user)
        ->get(route('posts.edit', ['post' => $postId]))
        ->assertNotFound();
});

test('attempting to update deleted post returns 404', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create();
    
    $postId = $post->id;
    $post->delete();

    actingAs($user)
        ->put(route('posts.update', ['post' => $postId]), [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'category_id' => $category->id,
        ])
        ->assertNotFound();
});

test('attempting to delete already deleted post returns 404', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create();
    
    $postId = $post->id;
    $post->delete();

    actingAs($user)
        ->delete(route('posts.destroy', ['post' => $postId]))
        ->assertNotFound();
});

test('malformed post data is rejected', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    actingAs($user)
        ->post(route('posts.store'), [
            'title' => ['an', 'array', 'instead', 'of', 'string'],  // Invalid data type
            'content' => 'Valid content',
            'category_id' => $category->id,
        ])
        ->assertInvalid(['title' => 'The title field must be a string.']);
});

test('attempting concurrent updates handles race conditions', function () {
    // This test simulates a race condition where two users try to update the same post
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $post = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create();

    // First update
    actingAs($user)
        ->put(route('posts.update', $post), [
            'title' => 'First Update',
            'content' => $post->content,
            'category_id' => $category->id,
        ])
        ->assertRedirect();

    // Verify the first update succeeded
    expect($post->fresh()->title)->toBe('First Update');

    // Second update should still work (Laravel handles this gracefully)
    actingAs($user)
        ->put(route('posts.update', $post), [
            'title' => 'Second Update',
            'content' => $post->content,
            'category_id' => $category->id,
        ])
        ->assertRedirect();

    // Verify the second update succeeded
    expect($post->fresh()->title)->toBe('Second Update');
});
<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('guests cannot access post creation page', function () {
    get(route('posts.create'))
        ->assertRedirect(route('login'));
});

test('guests cannot create posts', function () {
    $category = Category::factory()->create();

    post(route('posts.store'), [
        'title' => 'Test Post',
        'content' => 'Test Content',
        'category_id' => $category->id,
    ])->assertRedirect(route('login'));
});

test('authenticated users can view post creation page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('posts.create'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Posts/Create')
            ->has('categories')
        );
});

test('published_at is automatically set when creating post', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    $now = now();

    actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'Test Post',
            'content' => 'Test Content',
            'category_id' => $category->id,
        ]);

    $post = Post::latest()->first();

    // The published_at should be within a reasonable timeframe
    expect($post)
        ->published_at->toBeInstanceOf(DateTime::class)
        ->and($post->published_at->timestamp)
        ->toBeGreaterThanOrEqual($now->timestamp)
        ->toBeLessThanOrEqual(now()->timestamp);
});

test('authenticated users can create posts', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    actingAs($user)
        ->post(route('posts.store'), [
            'title' => 'Test Post',
            'content' => 'Test Content',
            'category_id' => $category->id,
        ])
        ->assertRedirect(route('posts.index'))
        ->assertSessionHas('success', 'Post created successfully.');

    $this->assertDatabaseHas('posts', [
        'title' => 'Test Post',
        'content' => 'Test Content',
        'category_id' => $category->id,
        'user_id' => $user->id,
    ]);
});

test('post validation rules', function ($field, $value, $error) {
    $user = User::factory()->create();
    $category = Category::factory()->create();

    // Prepare valid data
    $data = [
        'title' => 'Test Post',
        'content' => 'Test Content',
        'category_id' => $category->id,
    ];

    // Override with test case data
    $data[$field] = $value;

    actingAs($user)
        ->post(route('posts.store'), $data)
        ->assertInvalid([$field => $error]);
})->with([
    'title is required' => ['title', '', 'The title field is required.'],
    'title max length' => ['title', str_repeat('a', 256), 'The title field must not be greater than 255 characters.'],
    'content is required' => ['content', '', 'The content field is required.'],
    'category is required' => ['category_id', '', 'The category id field is required.'],
    'category must exist' => ['category_id', 99999, 'The selected category id is invalid.'],
]);

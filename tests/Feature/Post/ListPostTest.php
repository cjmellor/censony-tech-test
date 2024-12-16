<?php

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use function Pest\Laravel\{actingAs, get};

test('index page displays list of posts', function () {
    // Arrange
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $posts = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->count(3)
        ->create();

    // Act & Assert
    actingAs($user)
        ->get(route('posts.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Posts/Index')
            ->has('posts.data', 3)
            ->where('posts.data.0', fn ($post) => 
                is_string($post['title'])
                && is_string($post['content'])
                && !empty($post['published_at'])
                && !empty($post['author']['name'])
                && !empty($post['category']['name'])
            )
        );
});

test('posts are ordered by publication date in descending order', function () {
    // Arrange
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    // Create posts with different publication dates
    $oldPost = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create(['published_at' => now()->subDays(2)]);
        
    $newPost = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create(['published_at' => now()]);
        
    $middlePost = Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->create(['published_at' => now()->subDay()]);

    // Act & Assert
    actingAs($user)
        ->get(route('posts.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Posts/Index')
            ->where('posts.data.0.id', $newPost->id)
            ->where('posts.data.1.id', $middlePost->id)
            ->where('posts.data.2.id', $oldPost->id)
        );
});

test('posts list is paginated correctly', function () {
    // Arrange
    $user = User::factory()->create();
    $category = Category::factory()->create();
    
    // Create more than one page of posts
    Post::factory()
        ->for($user, 'author')
        ->for($category)
        ->count(15)
        ->create();
        
    // Act & Assert
    actingAs($user)
        ->get(route('posts.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Posts/Index')
            ->has('posts.data', 10) // First page should have 10 items
            ->has('posts.links') // Ensure pagination links are present
            ->where('posts.total', 15) // Total number of posts
            ->has('posts.current_page')
            ->has('posts.last_page')
        );

    // Test second page
    actingAs($user)
        ->get(route('posts.index', ['page' => 2]))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Posts/Index')
            ->has('posts.data', 5) // Second page should have remaining 5 items
            ->where('posts.current_page', 2)
        );
});
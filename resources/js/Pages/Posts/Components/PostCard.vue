<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    post: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
        <div class="p-6">
            <Link
                :href="route('posts.show', post.id)"
                class="text-xl font-semibold text-gray-900 hover:text-gray-600"
            >
                {{ post.title }}
            </Link>

            <div class="mt-2 text-sm text-gray-600">
                <div class="flex items-center space-x-2">
                    <span>By {{ post.author.name }}</span>
                    <span>•</span>
                    <span>{{ post.category.name }}</span>
                    <span>•</span>
                    <span>{{
                        new Date(post.published_at).toLocaleDateString()
                    }}</span>
                </div>
            </div>

            <p class="mt-4 line-clamp-3 text-gray-600">
                {{ post.content }}
            </p>

            <div class="mt-4 flex items-center space-x-4">
                <Link
                    :href="route('posts.show', post.id)"
                    class="text-sm text-blue-600 hover:text-blue-500"
                >
                    Read more
                </Link>
            </div>
        </div>
    </div>
</template>

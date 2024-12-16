<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

defineProps({
    post: {
        type: Object,
        required: true,
    },
    can: {
        type: Object,
        required: true,
    },
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

// Method to handle post deletion
const deletePost = (postId) => {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(route('posts.destroy', postId), {
            onSuccess: () => {
                // Optional: add any additional success handling
            },
        });
    }
};
</script>

<template>
    <Head :title="post.title" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-white">
            <div class="mx-auto max-w-3xl px-6 py-16 sm:py-24 lg:px-8">
                <!-- Back button with consistent spacing -->
                <div class="mb-12">
                    <Link
                        :href="route('posts.index')"
                        class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700"
                    >
                        <svg
                            class="mr-2 h-4 w-4"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Back
                    </Link>
                </div>

                <!-- Action buttons for authorized users -->
                <div
                    v-if="can.edit || can.delete"
                    class="mb-12 flex justify-end space-x-4"
                >
                    <Link
                        v-if="can.edit"
                        :href="route('posts.edit', post.id)"
                        class="inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600"
                    >
                        <svg
                            class="-ml-0.5 h-5 w-5 text-gray-400"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                            aria-hidden="true"
                        >
                            <path
                                d="M2.695 14.763l-1.262 3.154a.5.5 0 00.65.65l3.155-1.262a4 4 0 001.343-.885L17.5 5.5a2.121 2.121 0 00-3-3L3.58 13.42a4 4 0 00-.885 1.343z"
                            />
                        </svg>
                        Edit
                    </Link>
                    <button
                        v-if="can.delete"
                        @click="deletePost(post.id)"
                        class="inline-flex items-center gap-x-1.5 rounded-md bg-red-50 px-3 py-2 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-red-300 hover:bg-red-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="currentColor"
                            class="-ml-0.5 h-5 w-5 text-red-400"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M16.5 4.478v.227a48.816 48.816 0 013.878.512.75.75 0 11-.256 1.478l-.209-.035-1.005 13.07a3 3 0 01-2.991 2.77H8.084a3 3 0 01-2.991-2.77L4.087 6.66l-.209.035a.75.75 0 01-.256-1.478A48.567 48.567 0 017.5 4.705v-.227c0-1.564 1.213-2.9 2.816-2.951a52.662 52.662 0 013.369 0c1.603.051 2.815 1.387 2.815 2.951zm-6.136-1.452a51.196 51.196 0 013.273 0C14.39 3.05 15 3.684 15 4.478v.113a49.436 49.436 0 00-6 0v-.113c0-.794.609-1.428 1.364-1.452zm-.355 5.945a.75.75 0 10-1.5.058l.347 9a.75.75 0 101.499-.058l-.346-9zm5.48.058a.75.75 0 10-1.498-.058l-.347 9a.75.75 0 001.5.058l.345-9z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        Delete
                    </button>
                </div>

                <!-- Article header -->
                <header class="mb-16">
                    <!-- Date -->
                    <time
                        :datetime="post.published_at"
                        class="text-lg text-gray-600"
                    >
                        {{ formatDate(post.published_at) }}
                    </time>

                    <!-- Title -->
                    <h1
                        class="mt-6 text-[2.5rem] font-bold leading-tight tracking-tight text-gray-900 sm:text-[3.5rem]"
                    >
                        {{ post.title }}
                    </h1>
                </header>

                <!-- Article content -->
                <article class="prose prose-lg mx-auto">
                    <!-- Author and category info -->
                    <div
                        class="mb-12 flex items-center space-x-4 text-sm text-gray-600"
                    >
                        <span>By {{ post.author.name }}</span>
                        <span>•</span>
                        <span>{{ post.category.name }}</span>
                    </div>

                    <!-- Main content -->
                    <div class="space-y-6 leading-relaxed text-gray-700">
                        <p class="whitespace-pre-line">{{ post.content }}</p>
                    </div>
                </article>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

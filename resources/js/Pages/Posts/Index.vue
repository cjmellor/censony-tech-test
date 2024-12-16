<script setup>
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { onMounted, ref } from 'vue';

const props = defineProps({
    posts: {
        type: Object,
        required: true,
    },
});

const page = usePage();
const showSuccess = ref(true);

// Set up auto dismiss when component mounts
onMounted(() => {
    if (page.props.flash.success) {
        setTimeout(() => {
            showSuccess.value = false;
        }, 5000); // 5 seconds
    }
});

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
    <Head title="Posts" />

    <AuthenticatedLayout>
        <div class="bg-white py-24 sm:py-32">
            <div class="mx-auto max-w-7xl px-6 lg:px-8">
                <div class="mx-auto max-w-2xl">
                    <!-- Success Message -->
                    <div
                        v-if="page.props.flash?.success && showSuccess"
                        class="mb-8 rounded-md bg-green-50 p-4 transition-opacity duration-300"
                    >
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg
                                    class="h-5 w-5 text-green-400"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm font-medium text-green-800">
                                    {{ page.props.flash.success }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Header with title and Create button -->
                    <div class="mb-8 flex items-center justify-between">
                        <div>
                            <h2
                                class="text-pretty text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl"
                            >
                                Blog Posts
                            </h2>
                            <p class="mt-2 text-lg/8 text-gray-600">
                                Share your thoughts and ideas with the
                                community.
                            </p>
                        </div>
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('posts.create')"
                        >
                            <PrimaryButton> New Post</PrimaryButton>
                        </Link>
                    </div>

                    <!-- Posts list -->
                    <div
                        class="mt-10 space-y-16 border-t border-gray-200 pt-10 sm:mt-16 sm:pt-16"
                    >
                        <article
                            v-for="post in props.posts.data"
                            :key="post.id"
                            class="relative flex max-w-xl flex-col items-start justify-between"
                        >
                            <div class="group relative w-full">
                                <h3
                                    class="mt-3 text-lg/6 font-semibold text-gray-900 group-hover:text-gray-600"
                                >
                                    <Link :href="route('posts.show', post.id)">
                                        <span class="absolute inset-0" />
                                        {{ post.title }}
                                    </Link>
                                </h3>
                                <p
                                    class="mt-5 line-clamp-3 text-sm/6 text-gray-600"
                                >
                                    {{ post.content }}
                                </p>
                            </div>

                            <div
                                class="relative mt-8 flex w-full items-center gap-x-4"
                            >
                                <div
                                    class="flex size-10 items-center justify-center rounded-full bg-gray-50 font-semibold text-gray-600"
                                >
                                    {{
                                        post.author.name.charAt(0).toUpperCase()
                                    }}
                                </div>
                                <div class="flex-grow text-sm/6">
                                    <p class="font-semibold text-gray-900">
                                        {{ post.author.name }}
                                    </p>
                                </div>

                                <!-- Conditional Edit and Delete Buttons -->
                                <div
                                    v-if="
                                        $page.props.auth.user?.id ===
                                        post.author.id
                                    "
                                    class="flex items-center space-x-2"
                                >
                                    <Link
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
                                        @click="deletePost(post.id)"
                                        class="inline-flex items-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-red-600 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-red-50 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-600"
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
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

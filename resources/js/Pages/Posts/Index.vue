<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { onMounted, ref } from 'vue';

defineProps({
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

                    <!-- Rest of your template remains the same -->
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
                            v-for="post in posts.data"
                            :key="post.id"
                            class="flex max-w-xl flex-col items-start justify-between"
                        >
                            <div class="flex items-center gap-x-4 text-xs">
                                <time
                                    :datetime="post.published_at"
                                    class="text-gray-500"
                                >
                                    {{
                                        new Date(
                                            post.published_at,
                                        ).toLocaleDateString('en-US', {
                                            year: 'numeric',
                                            month: 'long',
                                            day: 'numeric',
                                        })
                                    }}
                                </time>
                                <span
                                    class="relative z-10 rounded-full bg-gray-50 px-3 py-1.5 font-medium text-gray-600 hover:bg-gray-100"
                                >
                                    {{ post.category.name }}
                                </span>
                            </div>

                            <div class="group relative">
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
                                class="relative mt-8 flex items-center gap-x-4"
                            >
                                <div
                                    class="flex size-10 items-center justify-center rounded-full bg-gray-50 font-semibold text-gray-600"
                                >
                                    {{
                                        post.author.name.charAt(0).toUpperCase()
                                    }}
                                </div>
                                <div class="text-sm/6">
                                    <p class="font-semibold text-gray-900">
                                        {{ post.author.name }}
                                    </p>
                                </div>
                            </div>
                        </article>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

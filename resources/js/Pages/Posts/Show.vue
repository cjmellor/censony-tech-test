<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

defineProps({
    post: {
        type: Object,
        required: true
    },
    can: {
        type: Object,
        required: true
    }
});

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
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
                        <svg class="mr-2 h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M7.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
                        </svg>
                        Back
                    </Link>
                </div>

                <!-- Action buttons for authorized users -->
                <div v-if="can.edit || can.delete" class="mb-12 flex justify-end space-x-4">
                    <Link v-if="can.edit" :href="route('posts.edit', post.id)">
                        <SecondaryButton>
                            Edit
                        </SecondaryButton>
                    </Link>
                    <Link v-if="can.delete" :href="route('posts.destroy', post.id)" method="delete" as="button">
                        <SecondaryButton type="button" class="!bg-red-50 !text-red-700 hover:!bg-red-100">
                            Delete
                        </SecondaryButton>
                    </Link>
                </div>

                <!-- Article header -->
                <header class="mb-16">
                    <!-- Date -->
                    <time :datetime="post.published_at" class="text-lg text-gray-600">
                        {{ formatDate(post.published_at) }}
                    </time>

                    <!-- Title -->
                    <h1 class="mt-6 text-[2.5rem] font-bold tracking-tight text-gray-900 sm:text-[3.5rem] leading-tight">
                        {{ post.title }}
                    </h1>
                </header>

                <!-- Article content -->
                <article class="prose prose-lg mx-auto">
                    <!-- Author and category info -->
                    <div class="mb-12 flex items-center space-x-4 text-sm text-gray-600">
                        <span>By {{ post.author.name }}</span>
                        <span>•</span>
                        <span>{{ post.category.name }}</span>
                    </div>

                    <!-- Main content -->
                    <div class="text-gray-700 leading-relaxed space-y-6">
                        <p class="whitespace-pre-line">{{ post.content }}</p>
                    </div>
                </article>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
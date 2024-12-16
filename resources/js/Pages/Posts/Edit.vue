<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PostForm from './Components/PostForm.vue';

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    title: props.post.title,
    content: props.post.content,
    category_id: props.post.category_id,
});

const submit = () => {
    form.patch(route('posts.update', props.post.id));
};
</script>

<template>
    <Head title="Edit Post" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Edit Post
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <PostForm
                            :form="form"
                            :categories="categories"
                            @submit="submit"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

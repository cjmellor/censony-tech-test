<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';

const props = defineProps({
    categories: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    title: '',
    content: '',
    category_id: '',
});

const submit = () => {
    form.post(route('posts.store'));
};
</script>

<template>
    <Head title="Create Post" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Create New Post
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-x-8 gap-y-8 md:grid-cols-3">
                            <!-- Left side description -->
                            <div class="px-4 sm:px-0">
                                <h2 class="text-base font-semibold text-gray-900">
                                    Create New Post
                                </h2>
                                <p class="mt-1 text-sm text-gray-600">
                                    Share your thoughts with the community. Your post will be published immediately.
                                </p>
                            </div>

                            <!-- Right side form -->
                            <form @submit.prevent="submit" class="md:col-span-2">
                                <div class="grid grid-cols-1 gap-y-6">
                                    <!-- Title Field -->
                                    <div>
                                        <InputLabel for="title" value="Title" />
                                        <TextInput
                                            id="title"
                                            v-model="form.title"
                                            type="text"
                                            class="mt-2 block w-full"
                                            required
                                        />
                                        <InputError :message="form.errors.title" class="mt-2" />
                                    </div>

                                    <!-- Category Selection -->
                                    <div>
                                        <InputLabel for="category" value="Category" />
                                        <select
                                            id="category"
                                            v-model="form.category_id"
                                            class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        >
                                            <option value="">Select a category</option>
                                            <option
                                                v-for="category in categories"
                                                :key="category.id"
                                                :value="category.id"
                                            >
                                                {{ category.name }}
                                            </option>
                                        </select>
                                        <InputError :message="form.errors.category_id" class="mt-2" />
                                    </div>

                                    <!-- Content Field -->
                                    <div>
                                        <InputLabel for="content" value="Content" />
                                        <textarea
                                            id="content"
                                            v-model="form.content"
                                            rows="6"
                                            class="mt-2 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                            required
                                        ></textarea>
                                        <InputError :message="form.errors.content" class="mt-2" />
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="mt-6 flex items-center justify-end gap-x-4">
                                    <Link :href="route('posts.index')">
                                        <SecondaryButton type="button">
                                            Cancel
                                        </SecondaryButton>
                                    </Link>
                                    <PrimaryButton :disabled="form.processing">
                                        Save Post
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
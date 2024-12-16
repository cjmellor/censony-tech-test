<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const props = defineProps({
    form: {
        type: Object,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
});

const emit = defineEmits(['submit']);

const handleSubmit = () => {
    emit('submit');
};
</script>

<template>
    <form @submit.prevent="handleSubmit" class="space-y-6">
        <div>
            <InputLabel for="title" value="Title" />
            <TextInput
                id="title"
                type="text"
                class="mt-1 block w-full"
                v-model="form.title"
                required
            />
            <InputError :message="form.errors.title" class="mt-2" />
        </div>

        <div>
            <InputLabel for="category" value="Category" />
            <select
                id="category"
                v-model="form.category_id"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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

        <div>
            <InputLabel for="content" value="Content" />
            <textarea
                id="content"
                v-model="form.content"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                rows="6"
                required
            ></textarea>
            <InputError :message="form.errors.content" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <PrimaryButton
                class="ml-4"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Save Post
            </PrimaryButton>
        </div>
    </form>
</template>

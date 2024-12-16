<script setup>
import { ref, watch } from 'vue';
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

// Create a local reactive data property to hold the form data
const localForm = ref({
    title: props.form.title,
    content: props.form.content,
    category_id: props.form.category_id,
});

// Watch for changes in the `form` prop and update the local form data accordingly
watch(
    () => props.form,
    (newForm) => {
        localForm.value = {
            title: newForm.title,
            content: newForm.content,
            category_id: newForm.category_id,
        };
    },
);

// When the form is submitted, emit the updated `localForm` data
const handleSubmit = () => {
    emit('submit', localForm.value);
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
                v-model="localForm.title"
                required
            />
            <InputError :message="props.form.errors.title" class="mt-2" />
        </div>

        <div>
            <InputLabel for="category" value="Category" />
            <select
                id="category"
                v-model="localForm.category_id"
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
            <InputError :message="props.form.errors.category_id" class="mt-2" />
        </div>

        <div>
            <InputLabel for="content" value="Content" />
            <textarea
                id="content"
                v-model="localForm.content"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                rows="6"
                required
            ></textarea>
            <InputError :message="props.form.errors.content" class="mt-2" />
        </div>

        <div class="flex items-center justify-end">
            <PrimaryButton
                class="ml-4"
                :class="{ 'opacity-25': props.form.processing }"
                :disabled="props.form.processing"
            >
                Save Post
            </PrimaryButton>
        </div>
    </form>
</template>

<template>
    <Teleport to="body">
        <div v-if="show" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">

            <div class="bg-white rounded-lg shadow-xl w-full max-w-lg mx-auto overflow-hidden">

                <form @submit.prevent="submit">

                    <div class="px-6 py-4 border-b bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-800">
                            {{ isEditing ? 'Edit ' + modelName : 'Create ' + modelName }}
                        </h3>
                    </div>

                    <div class="px-6 py-4 space-y-4">
                        <slot />
                    </div>

                    <div class="px-6 py-4 border-t flex justify-end gap-3 bg-gray-50">
                        <button
                            type="button"
                            @click="$emit('close')"
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 disabled:opacity-50"
                        >
                            <span v-if="form.processing">Saving...</span>
                            <span v-else>{{ isEditing ? 'Update' : 'Save' }}</span>
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
const props = defineProps({
    show: Boolean,
    isEditing: Boolean,
    modelName: { type: String, default: 'Item' }, // e.g., "Federation"
    actionUrl: String,
    form: Object, // The Inertia useForm object from the parent
});

const emit = defineEmits(['close']);

const submit = () => {
    if (props.isEditing) {
        // If editing, use PUT
        props.form.put(props.actionUrl, {
            onSuccess: () => emit('close'),
        });
    } else {
        // If creating, use POST
        props.form.post(props.actionUrl, {
            onSuccess: () => emit('close'),
        });
    }
};
</script>

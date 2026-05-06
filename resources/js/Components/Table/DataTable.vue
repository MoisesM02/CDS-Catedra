<template>
    <div class="overflow-x-auto w-full bg-white rounded shadow">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 uppercase text-xs font-semibold text-gray-600">
            <tr>
                <slot name="header" />
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
            <slot />
            </tbody>
        </table>
    </div>
</template>

<script setup>
const props = defineProps({
    columns: Array,
    sort: {
        type: String,
        default: '',
    },
    direction: {
        type: String,
        default: 'asc',
    }
});

const emit = defineEmits(['sort']);

const sortBy = (key) => {
    emit('sort', key);
};
</script>

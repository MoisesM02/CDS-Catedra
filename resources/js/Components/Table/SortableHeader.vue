<template>
    <th
        @click="$emit('sort', name)"
        class="px-6 py-3 cursor-pointer hover:bg-gray-100 select-none group"
    >
        <div class="flex items-center gap-1">
            <slot />

            <span v-if="sort === name" class="text-blue-600 font-bold">
                {{ direction === 'asc' ? '↑' : '↓' }}
            </span>

            <span v-else class="text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">
                ↑
            </span>
        </div>
    </th>
</template>

<script setup>
defineProps({
    name: {
        type: String,
        required: true,
    },
    sort: {
        type: String,
        default: '',
    },
    direction: {
        type: String,
        default: '',
    }
});

defineEmits(['sort']);
</script>

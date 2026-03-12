<template>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col overflow-hidden transition-shadow hover:shadow-md">

        <div v-if="$slots.header || title" class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <slot name="header">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ title }}</h3>
                    <p v-if="subtitle" class="text-sm text-gray-500">{{ subtitle }}</p>
                </div>
            </slot>
        </div>

        <div class="p-6 flex-grow flex flex-col justify-center">
            <slot />
        </div>

        <div v-if="$slots.footer" class="px-6 py-3 bg-gray-50 border-t border-gray-100 text-sm">
            <slot name="footer" />
        </div>

    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        required: false,
    },
    subtitle: {
        type: String,
        required: false,
    }
});
</script>

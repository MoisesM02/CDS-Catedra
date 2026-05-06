<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center rounded-md border border-white bg-red-600 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white shadow-sm transition duration-150 ease-in-out hover:bg-red-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25"
    >
        <slot />
    </button>
</template>

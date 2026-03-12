<template>
    <select
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full"
    >
        <option v-if="placeholder" value="" disabled>
            {{ placeholder }}
        </option>
        <slot/>
        <option
            v-for="option in options"
            :key="option[valueKey]"
            :value="option[valueKey]"
        >
            {{ option[labelKey] }}
        </option>
    </select>
</template>

<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: ''
    },
    // 1. Make options optional and default to an empty array
    options: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: 'Select an option...'
    },
    valueKey: {
        type: String,
        default: 'id'
    },
    labelKey: {
        type: String,
        default: 'name'
    }
});

defineEmits(['update:modelValue']);
</script>

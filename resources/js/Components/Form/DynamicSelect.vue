<template>
    <select
        :value="modelValue"
        @change="$emit('update:modelValue', $event.target.value)"
        class="border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-md shadow-sm w-full"
    >
        <option v-if="placeholder" value="" disabled>
            {{ placeholder }}
        </option>

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
    options: {
        type: Array,
        required: true,
    },
    placeholder: {
        type: String,
        default: 'Select an option...'
    },
    // The database column to use as the actual value (e.g., 'id')
    valueKey: {
        type: String,
        default: 'id'
    },
    // The database column to display to the user (e.g., 'name')
    labelKey: {
        type: String,
        default: 'name'
    }
});

defineEmits(['update:modelValue']);
</script>

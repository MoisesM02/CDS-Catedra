<template>
    <!-- Vue Transition for smooth slide-in and fade-out animations -->
    <Transition
        enter-active-class="transform ease-out duration-300 transition"
        enter-from-class="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
        enter-to-class="translate-y-0 opacity-100 sm:translate-x-0"
        leave-active-class="transition ease-in duration-100"
        leave-from-class="opacity-100"
        leave-to-class="opacity-0"
    >
        <!-- The actual Toast container fixed to the bottom right -->
        <div
            v-if="show"
            class="fixed bottom-4 right-4 z-50 flex items-center w-full max-w-xs p-4 space-x-3 text-gray-500 bg-white rounded-lg shadow-lg border-l-4 border-green-500"
            role="alert"
        >
            <!-- Success Checkmark Icon -->
            <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-green-500 bg-green-100 rounded-lg">
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5Zm3.707 8.207-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L9 10.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z"/>
                </svg>
                <span class="sr-only">Check icon</span>
            </div>

            <!-- Message Text -->
            <div class="ms-3 text-sm font-normal text-gray-800">{{ message }}</div>

            <!-- Manual Close Button -->
            <button
                @click="show = false"
                type="button"
                class="ms-auto -mx-1.5 -my-1.5 bg-white text-gray-400 hover:text-gray-900 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-gray-100 inline-flex items-center justify-center h-8 w-8"
                aria-label="Close"
            >
                <span class="sr-only">Close</span>
                <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                </svg>
            </button>
        </div>
    </Transition>
</template>

<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();
const show = ref(false);
const message = ref('');
let timeout = null;

// Watch the Inertia page props for changes to the flash message
// This triggers every time Inertia completes a visit (like after a form submission redirect)
watch(() => page.props.flash?.success, (newSuccessMessage) => {
    if (newSuccessMessage) {
        message.value = newSuccessMessage;
        show.value = true;

        // Clear any existing timeout so it doesn't close prematurely if triggered twice
        if (timeout) clearTimeout(timeout);

        // Auto-hide after 3.5 seconds
        timeout = setTimeout(() => {
            show.value = false;
        }, 3500);
    }
}, { immediate: true });
// { immediate: true } ensures it checks the prop immediately on first page load too
</script>

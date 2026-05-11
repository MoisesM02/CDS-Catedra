
<template>
    <AuthenticatedLayout>
        <Head>
            <title>{{ team?.name || 'Show Team' }}</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- Federation Information Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Federation Information</h3>
                    <div class="flex items-center gap-6">
                        <img
                            v-if="team.federation?.logo"
                            :src="team.federation.logo"
                            :alt="team.federation.name"
                            class="w-24 h-24 object-contain bg-gray-50 rounded border"
                        >
                        <div>
                            <p class="text-xl font-bold text-gray-900">{{ team.federation?.name }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-semibold">Founded:</span> {{ formatDate(team.federation?.date_of_foundation) }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1 whitespace-pre-line">
                                <span class="font-semibold">Address:</span> {{ team.federation?.address }}
                            </p>
                            <div class="mt-4 inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                Total Players: {{ team.players?.length || 0 }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Players Table Card using Custom Components -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Team Roster</h3>
                    </div>

                    <DataTable>
                        <template #header>
                            <!-- Notice how we pass the reactive sortParams -->
                            <SortableHeader
                                name="name"
                                :sort="sortParams.sort"
                                :direction="sortParams.direction"
                                @sort="handleSort"
                            >
                                Name
                            </SortableHeader>

                            <SortableHeader
                                name="date_of_birth"
                                :sort="sortParams.sort"
                                :direction="sortParams.direction"
                                @sort="handleSort"
                            >
                                Date of Birth
                            </SortableHeader>

                            <SortableHeader
                                name="gender"
                                :sort="sortParams.sort"
                                :direction="sortParams.direction"
                                @sort="handleSort"
                            >
                                Gender
                            </SortableHeader>
                        </template>

                        <!-- Iterate over sortedPlayers instead of team.players -->
                        <tr v-for="player in sortedPlayers" :key="player.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ player.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(player.date_of_birth) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                {{ player.gender }}
                            </td>
                        </tr>

                        <!-- Empty State -->
                        <tr v-if="sortedPlayers.length === 0">
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">
                                No players are currently registered to this team.
                            </td>
                        </tr>
                    </DataTable>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/Table/DataTable.vue";
import SortableHeader from "@/Components/Table/SortableHeader.vue";


const props = defineProps({
    team: Object
});

// Helper to format dates
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString();
};

// Setup sorting state
const sortParams = reactive({
    sort: 'name',     // Default sort column
    direction: 'asc', // Default sort direction
});

// Handle sort clicks from the SortableHeader component
const handleSort = (key) => {
    if (sortParams.sort === key) {
        // Toggle direction if clicking the same column
        sortParams.direction = sortParams.direction === 'asc' ? 'desc' : 'asc';
    } else {
        // Set new column and default to ascending
        sortParams.sort = key;
        sortParams.direction = 'asc';
    }
};

// Create a computed property to handle the actual sorting of the array
const sortedPlayers = computed(() => {
    // Return empty array if players aren't loaded
    if (!props.team?.players) return [];

    // Create a copy of the array so we don't mutate the original prop
    return [...props.team.players].sort((a, b) => {
        let modifier = sortParams.direction === 'asc' ? 1 : -1;

        let valA = a[sortParams.sort];
        let valB = b[sortParams.sort];

        // Handle string comparison nicely
        if (typeof valA === 'string') valA = valA.toLowerCase();
        if (typeof valB === 'string') valB = valB.toLowerCase();

        if (valA < valB) return -1 * modifier;
        if (valA > valB) return 1 * modifier;
        return 0;
    });
});
</script>


<template>
    <AuthenticatedLayout>
        <Head>
            <title>{{ player?.name || 'Show Player' }}</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- 1. Player Header -->
                <BackgroundCard class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ player.name }}</h2>
                        <p class="text-sm text-gray-500 mt-1 capitalize">
                            {{ player.gender }} • Born: {{ formatDate(player.date_of_birth) }}
                            ({{ calculateAge(player.date_of_birth) }} years old)
                        </p>
                    </div>

                    <!-- Current Status Badge -->
                    <div
                        v-if="player.current_team?.[0]"
                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-blue-100 text-blue-800 shadow-sm border border-blue-200"
                    >
                        Active Player: {{ player.current_team[0].name }}
                    </div>
                    <div
                        v-else
                        class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-gray-100 text-gray-800 shadow-sm border border-gray-200"
                    >
                        Free Agent
                    </div>
                </BackgroundCard>

                <!-- 2. Current Contract Information -->
                <BackgroundCard v-if="player.current_team?.[0]">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Current Contract</h3>

                    <div class="flex flex-col sm:flex-row gap-8">
                        <!-- Team Info -->
                        <div>
                            <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Club</p>
                            <p class="text-xl font-bold text-gray-900 mt-1">{{ player.current_team[0].name }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-semibold">Signed on:</span>
                                {{ player.current_team[0].pivot?.start_date ? formatDate(player.current_team[0].pivot.start_date) : 'Unknown Date' }}
                            </p>
                        </div>

                        <!-- Federation Info (Nested inside the current team) -->
                        <div v-if="player.current_team[0].federation" class="sm:border-l sm:pl-8">
                            <p class="text-sm text-gray-500 uppercase tracking-wider font-semibold">Federation</p>
                            <div class="flex items-center gap-3 mt-2">
                                <img
                                    v-if="player.current_team[0].federation.logo"
                                    :src="player.current_team[0].federation.logo"
                                    :alt="player.current_team[0].federation.name"
                                    class="w-10 h-10 object-contain bg-gray-50 rounded border"
                                >
                                <p class="text-base font-medium text-gray-900">
                                    {{ player.current_team[0].federation.name }}
                                </p>
                            </div>
                        </div>
                    </div>
                </BackgroundCard>

                <!-- 3. Career History Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Career History</h3>
                    </div>

                    <DataTable>
                        <template #header>
                            <SortableHeader name="name" :sort="sortParams.sort" :direction="sortParams.direction" @sort="handleSort">
                                Team
                            </SortableHeader>
                            <SortableHeader name="federation.name" :sort="sortParams.sort" :direction="sortParams.direction" @sort="handleSort">
                                Federation
                            </SortableHeader>
                            <SortableHeader name="pivot.start_date" :sort="sortParams.sort" :direction="sortParams.direction" @sort="handleSort">
                                Date Joined
                            </SortableHeader>
                            <SortableHeader name="pivot.end_date" :sort="sortParams.sort" :direction="sortParams.direction" @sort="handleSort">
                                Date Left
                            </SortableHeader>
                        </template>

                        <tr v-for="team in sortedHistory" :key="team.pivot.start_date" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ team.name }}
                                <!-- Tiny badge to highlight their current team in the history list -->
                                <span v-if="!team.pivot.end_date" class="ml-2 px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-800">
                                    CURRENT
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ team.federation?.name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ formatDate(team.pivot?.start_date) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ team.pivot?.end_date ? formatDate(team.pivot.end_date) : 'Present' }}
                            </td>
                        </tr>

                        <tr v-if="sortedHistory.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                No historical records found for this player.
                            </td>
                        </tr>
                    </DataTable>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { reactive, computed } from 'vue';
import { Head } from '@inertiajs/vue3'
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";

import DataTable from "@/Components/Table/DataTable.vue";
import SortableHeader from "@/Components/Table/SortableHeader.vue";
import BackgroundCard from "@/Components/Utilities/BackgroundCard.vue";

const props = defineProps({
    player: Object
});

// Helper to format dates cleanly
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString();
};

// Helper to calculate age dynamically based on DOB
const calculateAge = (dob) => {
    if (!dob) return '';
    const diff = Date.now() - new Date(dob).getTime();
    const age = new Date(diff);
    return Math.abs(age.getUTCFullYear() - 1970);
};

// Helper to get nested object properties (Needed for sorting by pivot.start_date or federation.name)
const resolvePath = (object, path) => {
    return path.split('.').reduce((o, p) => o ? o[p] : '', object);
};

// --- SORTING LOGIC FOR HISTORY TABLE ---
// Default to showing the newest contracts first
const sortParams = reactive({ sort: 'pivot.start_date', direction: 'desc' });

const handleSort = (key) => {
    if (sortParams.sort === key) {
        sortParams.direction = sortParams.direction === 'asc' ? 'desc' : 'asc';
    } else {
        sortParams.sort = key;
        sortParams.direction = 'asc';
    }
};

const sortedHistory = computed(() => {
    if (!props.player?.teams) return [];

    return [...props.player.teams].sort((a, b) => {
        let modifier = sortParams.direction === 'asc' ? 1 : -1;

        let valA = resolvePath(a, sortParams.sort);
        let valB = resolvePath(b, sortParams.sort);

        // Handle null values gracefully (e.g., end_date is null for the active team)
        if (valA === null || valA === undefined) valA = '';
        if (valB === null || valB === undefined) valB = '';

        if (typeof valA === 'string') valA = valA.toLowerCase();
        if (typeof valB === 'string') valB = valB.toLowerCase();

        if (valA < valB) return -1 * modifier;
        if (valA > valB) return 1 * modifier;
        return 0;
    });
});
</script>

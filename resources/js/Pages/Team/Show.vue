<template>
    <AuthenticatedLayout>
        <Head>
            <title>{{ team?.name || 'Show Team' }}</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- 1. Team Info Header Card -->
                <BackgroundCard class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ team.name }}</h2>
                        <p class="text-sm text-gray-500 mt-1">Official Team Roster & Records</p>
                    </div>

                    <div class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold bg-blue-100 text-blue-800 shadow-sm border border-blue-200">
                        Active Players: {{ team.current_players?.length || 0 }}
                    </div>
                </BackgroundCard>

                <!-- 2. Federation Information Card (Inline) -->
                <BackgroundCard>
                    <h3 class="text-lg font-bold text-gray-900 mb-4 border-b pb-2">Federation Information</h3>

                    <!-- Only show if a federation exists -->
                    <div v-if="team.federation" class="flex items-center gap-6">
                        <img
                            v-if="team.federation.logo"
                            :src="team.federation.logo"
                            :alt="team.federation.name"
                            class="w-24 h-24 object-contain bg-gray-50 rounded border"
                        >
                        <div>
                            <p class="text-xl font-bold text-gray-900">{{ team.federation.name }}</p>
                            <p class="text-sm text-gray-600 mt-1">
                                <span class="font-semibold">Founded:</span> {{ formatDate(team.federation.date_of_foundation) }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1 whitespace-pre-line">
                                <span class="font-semibold">Address:</span> {{ team.federation.address }}
                            </p>
                        </div>
                    </div>

                    <!-- Fallback if the team has no federation -->
                    <div v-else class="text-gray-500 text-sm italic">
                        This team is not currently affiliated with a federation.
                    </div>
                </BackgroundCard>

                <!-- 1. Current Players Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900">Current Roster</h3>
                    </div>

                    <DataTable>
                        <template #header>
                            <SortableHeader name="name" :sort="currentSort.sort" :direction="currentSort.direction" @sort="handleCurrentSort">
                                Name
                            </SortableHeader>
                            <SortableHeader name="date_of_birth" :sort="currentSort.sort" :direction="currentSort.direction" @sort="handleCurrentSort">
                                Date of Birth
                            </SortableHeader>
                            <SortableHeader name="gender" :sort="currentSort.sort" :direction="currentSort.direction" @sort="handleCurrentSort">
                                Gender
                            </SortableHeader>
                            <!-- New Date Signed Column -->
                            <SortableHeader name="pivot.start_date" :sort="currentSort.sort" :direction="currentSort.direction" @sort="handleCurrentSort">
                                Date Signed
                            </SortableHeader>
                        </template>

                        <tr v-for="player in sortedCurrentPlayers" :key="player.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ player.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(player.date_of_birth) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ player.gender }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-blue-600">{{ formatDate(player.pivot?.start_date) }}</td>
                        </tr>

                        <tr v-if="sortedCurrentPlayers.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                No players are currently registered to this team.
                            </td>
                        </tr>
                    </DataTable>
                </div>

                <!-- 2. Past Players Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Past Players</h3>
                    </div>

                    <DataTable>
                        <template #header>
                            <SortableHeader name="name" :sort="pastSort.sort" :direction="pastSort.direction" @sort="handlePastSort">
                                Name
                            </SortableHeader>
                            <SortableHeader name="pivot.start_date" :sort="pastSort.sort" :direction="pastSort.direction" @sort="handlePastSort">
                                Date Joined
                            </SortableHeader>
                            <SortableHeader name="pivot.end_date" :sort="pastSort.sort" :direction="pastSort.direction" @sort="handlePastSort">
                                Date Left
                            </SortableHeader>
                            <th class="px-6 py-3 cursor-default select-none uppercase text-xs font-semibold text-gray-600">
                                Current Team
                            </th>
                        </template>

                        <tr v-for="player in sortedPastPlayers" :key="player.id" class="hover:bg-gray-50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ player.name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(player.pivot?.start_date) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(player.pivot?.end_date) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <!-- Uses the safely chained property for current_team -->
                                <span v-if="player.current_team?.[0]" class="px-2 py-1 bg-green-100 text-green-800 rounded-md text-xs font-bold">
                                    {{ player.current_team[0].name }}
                                </span>
                                <span v-else class="px-2 py-1 bg-gray-100 text-gray-600 rounded-md text-xs font-bold">
                                    Free Agent
                                </span>
                            </td>
                        </tr>

                        <tr v-if="sortedPastPlayers.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-sm text-gray-500">
                                This team has no past players.
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
    team: Object
});

// Helper to format dates
const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Intl.DateTimeFormat('es-SV').format(new Date(dateString));
};

// Helper to get nested object properties (Needed for sorting by pivot.start_date)
const resolvePath = (object, path) => {
    return path.split('.').reduce((o, p) => o ? o[p] : '', object);
};

// --- CURRENT PLAYERS SORTING ---
const currentSort = reactive({ sort: 'name', direction: 'asc' });

const handleCurrentSort = (key) => {
    if (currentSort.sort === key) {
        currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        currentSort.sort = key;
        currentSort.direction = 'asc';
    }
};

const sortedCurrentPlayers = computed(() => {
    if (!props.team?.current_players) return [];

    return [...props.team.current_players].sort((a, b) => {
        let modifier = currentSort.direction === 'asc' ? 1 : -1;
        // Use resolvePath to allow sorting by "pivot.start_date"
        let valA = resolvePath(a, currentSort.sort);
        let valB = resolvePath(b, currentSort.sort);

        if (typeof valA === 'string') valA = valA.toLowerCase();
        if (typeof valB === 'string') valB = valB.toLowerCase();

        if (valA < valB) return -1 * modifier;
        if (valA > valB) return 1 * modifier;
        return 0;
    });
});

// --- PAST PLAYERS SORTING ---
const pastSort = reactive({ sort: 'pivot.end_date', direction: 'desc' }); // Default to most recently left

const handlePastSort = (key) => {
    if (pastSort.sort === key) {
        pastSort.direction = pastSort.direction === 'asc' ? 'desc' : 'asc';
    } else {
        pastSort.sort = key;
        pastSort.direction = 'asc';
    }
};

const sortedPastPlayers = computed(() => {
    if (!props.team?.past_players) return [];

    return [...props.team.past_players].sort((a, b) => {
        let modifier = pastSort.direction === 'asc' ? 1 : -1;

        let valA = resolvePath(a, pastSort.sort);
        let valB = resolvePath(b, pastSort.sort);

        if (typeof valA === 'string') valA = valA.toLowerCase();
        if (typeof valB === 'string') valB = valB.toLowerCase();

        if (valA < valB) return -1 * modifier;
        if (valA > valB) return 1 * modifier;
        return 0;
    });
});
</script>

<template>
    <AuthenticatedLayout>
        <Head>
            <title>{{ federation?.name || 'Show Federation' }}</title>
        </Head>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                <!-- 1. Federation Header & Stats -->
                <BackgroundCard class="flex flex-col md:flex-row gap-6 items-center md:items-start justify-between">

                    <!-- Left: Basic Info -->
                    <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6 text-center sm:text-left">
                        <img
                            v-if="federation.logo"
                            :src="federation.logo"
                            :alt="federation.name"
                            class="w-24 h-24 object-contain bg-gray-50 rounded-lg border shadow-sm shrink-0"
                        >
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">{{ federation.name }}</h2>
                            <p class="text-sm text-gray-600 mt-2">
                                <span class="font-semibold">Founded:</span> {{ formatDate(federation.date_of_foundation) }}
                            </p>
                            <p class="text-sm text-gray-600 mt-1 whitespace-pre-line">
                                <span class="font-semibold">Headquarters:</span> {{ federation.address }}
                            </p>
                        </div>
                    </div>

                    <!-- Right: Quick Statistics -->
                    <div class="flex gap-4 w-full md:w-auto">
                        <div class="flex-1 bg-blue-50 border border-blue-100 rounded-lg p-4 text-center min-w-[120px]">
                            <span class="block text-3xl font-bold text-blue-700">{{ stats.total_teams }}</span>
                            <span class="block text-xs font-semibold uppercase tracking-wider text-blue-600 mt-1">Clubs</span>
                        </div>
                        <div class="flex-1 bg-green-50 border border-green-100 rounded-lg p-4 text-center min-w-[120px]">
                            <span class="block text-3xl font-bold text-green-700">{{ stats.total_active_players }}</span>
                            <span class="block text-xs font-semibold uppercase tracking-wider text-green-600 mt-1">Active Players</span>
                        </div>
                    </div>
                </BackgroundCard>

                <!-- 2. Affiliated Teams Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900">Affiliated Clubs</h3>
                    </div>

                    <DataTable>
                        <template #header>
                            <SortableHeader name="name" :sort="sortParams.sort" :direction="sortParams.direction" @sort="handleSort">
                                Club Name
                            </SortableHeader>
                            <!-- Notice we use the alias from the database controller here -->
                            <SortableHeader name="active_players_count" :sort="sortParams.sort" :direction="sortParams.direction" @sort="handleSort">
                                Active Players
                            </SortableHeader>
                            <th class="px-6 py-3 text-right cursor-default select-none uppercase text-xs font-semibold text-gray-600">
                                Actions
                            </th>
                        </template>

                        <tr v-for="team in sortedTeams" :key="team.id" class="hover:bg-gray-50 transition-colors">
                            <!-- Team Name (Using the padding fix so the whole cell is clickable) -->
                            <td class="whitespace-nowrap text-sm font-medium p-0">
                                <Link
                                    :href="route('team.show', team.id)"
                                    class="block px-6 py-4 text-blue-600 hover:text-blue-900 hover:bg-blue-50 transition-colors"
                                >
                                    {{ team.name }}
                                </Link>
                            </td>

                            <!-- Player Count -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                {{ team.active_players_count }} Registered
                            </td>

                            <!-- Quick Action Button -->
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-right font-medium">
                                <Link
                                    :href="route('team.show', team.id)"
                                    class="text-gray-600 hover:text-gray-900 bg-white border border-gray-300 rounded-md px-3 py-1.5 shadow-sm text-xs font-bold transition-all hover:bg-gray-50"
                                >
                                    View Roster
                                </Link>
                            </td>
                        </tr>
                        <!-- Empty State -->
                        <tr v-if="sortedTeams.length === 0">
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500">
                                No teams are currently affiliated with this federation.
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
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import DataTable from "@/Components/Table/DataTable.vue";
import SortableHeader from "@/Components/Table/SortableHeader.vue";
import BackgroundCard from "@/Components/Utilities/BackgroundCard.vue";

const props = defineProps({
    federation: Object,
    stats: Object // Receiving the new stats array from the controller
});


const formatDate = (dateString) => {
    if (!dateString) return '';
    return new Date(dateString).toLocaleDateString();
};

// --- SORTING LOGIC FOR TEAMS ---
const sortParams = reactive({ sort: 'name', direction: 'asc' });

const handleSort = (key) => {
    if (sortParams.sort === key) {
        sortParams.direction = sortParams.direction === 'asc' ? 'desc' : 'asc';
    } else {
        sortParams.sort = key;
        sortParams.direction = 'asc';
    }
};

const sortedTeams = computed(() => {
    if (!props.federation?.teams) return [];

    return [...props.federation.teams].sort((a, b) => {
        let modifier = sortParams.direction === 'asc' ? 1 : -1;

        let valA = a[sortParams.sort];
        let valB = b[sortParams.sort];

        // Ensure numbers sort correctly
        if (typeof valA === 'number' && typeof valB === 'number') {
            return (valA - valB) * modifier;
        }

        // Handle string comparison
        if (typeof valA === 'string') valA = valA.toLowerCase();
        if (typeof valB === 'string') valB = valB.toLowerCase();

        if (valA < valB) return -1 * modifier;
        if (valA > valB) return 1 * modifier;
        return 0;
    });
});
</script>

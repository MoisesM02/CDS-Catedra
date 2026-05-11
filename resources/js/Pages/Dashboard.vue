<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from "@/Components/Utilities/Card.vue";
import { Head } from '@inertiajs/vue3';
import {forEach} from "lodash";

const props = defineProps({
    teams_data : Object,
    federations_data : Object,
    federations_most_teams: Object
})

props.federations_most_teams.forEach(fed =>{
    console.log(fed.name)
})
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800"
            >
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                        <Card title="Number of teams" subtitle="Active in a federation">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-gray-900">{{ teams_data.count}}</p>
                                    <p class="text-sm font-medium text-green-600"> +{{teams_data.newsCount}} ({{teams_data.percentage}}%) joined this month </p>
                                </div>
                            </div>
                        </Card>
                        <Card title="Number of federations" subtitle="With at least a team">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-blue-100 text-blue-600 rounded-lg">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                </div>
                                <div>
                                    <p class="text-3xl font-bold text-gray-900">{{ federations_data.count}}</p>
                                    <p class="text-sm font-medium text-green-600"> +{{federations_data.newsCount}} ({{federations_data.percentage}}%) joined this month</p>
                                </div>
                            </div>
                        </Card>
                        <Card title="Federations with the most teams">
                            <div class="flex items-center gap-4" v-for="fed in federations_most_teams">
                                <div>
                                    <p><span class="font-bold text-md">{{ fed.name }}: </span> <span>{{fed.teams_count}} team(s)</span>   </p>
                                </div>
                            </div>
                        </Card>
                    </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

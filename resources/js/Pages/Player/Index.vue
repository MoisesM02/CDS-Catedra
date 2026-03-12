<template>
    <AuthenticatedLayout>
        <div class="p-6 max-w-7xl mx-auto">
            <div class="flex justify-between mb-6">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search players..."
                    class="border rounded px-4 py-2 w-1/3"
                />
                <primary-button @click="openCreateModal">Create Player</primary-button>
            </div>

            <DataTable>

                <template #header>
                    <SortableHeader
                        name="name"
                        :sort="params.sort"
                        :direction="params.direction"
                        @sort="handleSort"
                    >
                        Player Name
                    </SortableHeader>

                    <SortableHeader
                        name="team_id"
                        :sort="params.sort"
                        :direction="params.direction"
                        @sort="handleSort"
                        class="w-48"
                    >
                        Team
                    </SortableHeader>
                    <SortableHeader
                        name="gender"
                        :sort="params.sort"
                        :direction="params.direction"
                        @sort="handleSort"
                        class="w-48"
                    >
                        Gender
                    </SortableHeader>
                    <SortableHeader
                        name="date_of_birth"
                        :sort="params.sort"
                        :direction="params.direction"
                        @sort="handleSort"
                        class="w-48"
                    >
                        Date of birth
                    </SortableHeader>
                    <th class="px-6 py-3 text-right w-32">
                        Actions
                    </th>
                </template>

                <tr v-for="player in players.data" :key="player.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ player.name }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ player.team.name}}</td>
                    <td class="px-6 py-4 text-gray-500">{{ player.gender}}</td>
                    <td class="px-6 py-4"> {{ new Date(player.date_of_birth).toLocaleDateString()}}</td>
                    <td class="px-6 py-4 text-right">
                        <secondary-button @click="openEditModal(player)">Edit</secondary-button>
                    </td>
                </tr>

            </DataTable>

            <Pagination :links="players.links" />

            <FormModal
                :show="isModalOpen"
                :is-editing="isEditing"
                model-name="Team"
                :action-url="actionUrl"
                :form="form"
                @close="closeModal"
            >
                <div>
                    <InputLabel for="name" value="Name" />

                    <TextInput
                        id="name"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autocomplete="name"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel for="team_id" value="Team" />

                    <DynamicSelect
                        v-model="form.team_id"
                        :options="teams"
                        placeholder="Select a Team..."
                    />
                    <InputError class="mt-2" :message="form.errors.team_id" />
                </div>
                <div>
                    <InputLabel for="date_of_birth" value="Date of birth" />

                    <TextInput
                        v-model="form.date_of_birth"
                        type="date"
                        id="date_of_birth"
                        class="mt-1 block w-full"
                        required/>
                    <InputError class="mt-2" :message="form.errors.date_of_birth" />
                </div>
                <div>
                    <InputLabel for="gender" value="Gender" />

                    <DynamicSelect
                        v-model="form.gender"
                        placeholder="Select a gender..."
                    >
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </DynamicSelect>
                    <InputError class="mt-2" :message="form.errors.team_id" />
                </div>
            </FormModal>
        </div>
    </AuthenticatedLayout>
</template>


<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import {Head, router, useForm} from '@inertiajs/vue3'
import Pagination from "@/Components/Pagination.vue";
import DataTable from "@/Components/Table/DataTable.vue";
import TextInput from "@/Components/TextInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SortableHeader from "@/Components/Table/SortableHeader.vue";
import InputError from "@/Components/InputError.vue";
import DynamicSelect from "@/Components/Form/DynamicSelect.vue";
import FormModal from "@/Components/FormModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import {computed, reactive, ref, watch} from "vue";
import debounce from "lodash/debounce";

const props = defineProps({
    players: Object,
    teams: Array,
    filters: Object,
})

const search = ref(props.filters.search);
const params = reactive({
    sort: props.filters.sort,
    direction: props.filters.direction,
});

// Update URL when sorting
const handleSort = (key) => {
    params.sort = key;
    params.direction = params.direction === 'asc' ? 'desc' : 'asc';
    updateQuery();
};

// Update URL when searching (Debounced to avoid too many requests)
watch(search, debounce((value) => {
    updateQuery();
}, 300));

const updateQuery = () => {
    router.get('/players', {
        search: search.value,
        sort: params.sort,
        direction: params.direction
    }, {
        preserveState: true,
        replace: true
    });
};

//Modal Form

// 1. Initialize the Form
const form = useForm({
    name: '',
    team_id: '',
    gender: '',
    date_of_birth: ''
});

// 2. Modal State Management
const isModalOpen = ref(false);
const editingModel = ref(null); // Will hold the item we are editing, or null if creating

// 3. Computed Properties for the Modal
const isEditing = computed(() => editingModel.value !== null);

const actionUrl = computed(() => {
    // Ensure you have these routes defined in web.php!
    return isEditing.value
        ? `/players/${editingModel.value.id}` // PUT route
        : `/players`;                         // POST route
});

// 4. Modal Triggers
const openCreateModal = () => {
    editingModel.value = null; // Set to Create mode
    form.reset();              // Clear inputs
    form.clearErrors();        // Clear old validation errors
    isModalOpen.value = true;
};

const openEditModal = (team) => {
    editingModel.value = team;  // Set to Edit mode
    form.name = team.name;      // Populate the form with existing data!
    form.federation_id = team.federation_id
    form.clearErrors();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => form.reset(), 300); // Reset form after transition ends
};
</script>

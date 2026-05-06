<template>
    <Head>
        <title>Teams</title>
    </Head>
    <AuthenticatedLayout>
        <div class="p-6 max-w-7xl mx-auto">
            <div class="flex justify-between mb-6">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search teams..."
                    class="border rounded px-4 py-2 w-1/3"
                />
                <primary-button @click="openCreateModal">Create Team</primary-button>
            </div>

            <DataTable>

                <template #header>
                    <SortableHeader
                        name="name"
                        :sort="params.sort"
                        :direction="params.direction"
                        @sort="handleSort"
                    >
                        Team Name
                    </SortableHeader>

                    <SortableHeader
                        name="federation_id"
                        :sort="params.sort"
                        :direction="params.direction"
                        @sort="handleSort"
                        class="w-48"
                    >
                        Federation
                    </SortableHeader>
                    <SortableHeader
                        name="created_at"
                        :sort="params.sort"
                        :direction="params.direction"
                        @sort="handleSort"
                        class="w-48"
                    >
                        Created At
                    </SortableHeader>
                    <th class="px-6 py-3 text-right w-32">
                        Actions
                    </th>
                </template>

                <tr v-for="team in teams.data" :key="team.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ team.name }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ team.federation.name}}</td>
                    <td class="px-6 py-4"> {{ new Date(team.created_at).toLocaleString()}}</td>
                    <td class="px-6 py-4 text-right">
                        <secondary-button @click="openEditModal(team)">Edit</secondary-button>
                    </td>
                </tr>

            </DataTable>

            <Pagination :links="teams.links" />

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
                        autocomplete="username"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>
                <div>
                    <InputLabel for="federation_id" value="Date of Foundation" />

                    <DynamicSelect
                        v-model="form.federation_id"
                        :options="feds"
                        placeholder="Select a Federation..."
                    />
                    <InputError class="mt-2" :message="form.errors.federation_id" />
                </div>
            </FormModal>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import {Head, router, useForm} from '@inertiajs/vue3'
import {computed, reactive, ref, watch} from "vue";
import debounce from 'lodash/debounce';
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Pagination from "@/Components/Pagination.vue";
import DataTable from "@/Components/Table/DataTable.vue";
import TextInput from "@/Components/TextInput.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SortableHeader from "@/Components/Table/SortableHeader.vue";
import InputError from "@/Components/InputError.vue";
import FormModal from "@/Components/FormModal.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Dropdown from "@/Components/Dropdown.vue";
import DynamicSelect from "@/Components/Form/DynamicSelect.vue";


const props = defineProps({
    teams: Object,
    feds: Array,
    filters: Object
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
    router.get('/teams', {
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
    federation_id: '',
});

// 2. Modal State Management
const isModalOpen = ref(false);
const editingModel = ref(null); // Will hold the item we are editing, or null if creating

// 3. Computed Properties for the Modal
const isEditing = computed(() => editingModel.value !== null);

const actionUrl = computed(() => {
    // Ensure you have these routes defined in web.php!
    return isEditing.value
        ? `/teams/${editingModel.value.id}` // PUT route
        : `/teams`;                         // POST route
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

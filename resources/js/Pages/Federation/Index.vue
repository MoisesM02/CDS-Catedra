<template>
    <AuthenticatedLayout>
        <div class="p-6 max-w-7xl mx-auto">
            <div class="flex justify-between mb-6">
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search federations..."
                    class="border rounded px-4 py-2 w-1/3"
                />
                <primary-button @click="openCreateModal">Create Federation</primary-button>
            </div>

            <DataTable>

                <template #header>
                    <SortableHeader
                        name="name"
                        :sort="params.sort"
                        :direction="params.direction"
                        @sort="handleSort"
                    >
                        Federation Name
                    </SortableHeader>

                    <SortableHeader
                        name="date_of_foundation"
                        :sort="params.sort"
                        :direction="params.direction"
                        @sort="handleSort"
                        class="w-48"
                    >
                        Foundation Date
                    </SortableHeader>
                    <SortableHeader
                        name="address"
                        :sort="params.sort"
                        :direction="params.direction"
                        @sort="handleSort"
                        class="w-48"
                    >
                        Address
                    </SortableHeader>
                    <SortableHeader
                        name="logo"
                        :sort="params.sort"
                        :direction="params.direction"
                        @sort="handleSort"
                        class="w-48"
                    >
                        Logo
                    </SortableHeader>


                    <th class="px-6 py-3 text-right w-32">
                        Actions
                    </th>
                </template>

                <tr v-for="fed in feds.data" :key="fed.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4">{{ fed.name }}</td>
                    <td class="px-6 py-4 text-gray-500">{{ new Date(fed.date_of_foundation).toLocaleDateString() }}</td>
                    <td class="px-6 py-4"> {{ fed.address}}</td>
                    <td class="px-6 py-4"> <img :src="fed.logo" :alt="fed.name"/>  </td>
                    <td class="px-6 py-4 text-right">
                        <secondary-button @click="openEditModal(fed)">Edit</secondary-button>
                    </td>
                </tr>

            </DataTable>

            <Pagination :links="feds.links" />

            <FormModal
                :show="isModalOpen"
                :is-editing="isEditing"
                model-name="Federation"
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
                    <InputLabel for="date_of_foundation" value="Date of Foundation" />

                    <TextInput
                        id="date_of_foundation"
                        type="date"
                        class="mt-1 block w-full"
                        v-model="form.date_of_foundation"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.date_of_foundation" />
                </div>
                <div>
                    <InputLabel for="address" value="Address" />

                    <TextInput
                        id="address"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.address"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.address" />
                </div>
                <div>
                    <InputLabel for="logo" value="Logo URL" />

                    <TextInput
                        id="logo"
                        type="text"
                        class="mt-1 block w-full"
                        v-model="form.logo"
                        required
                    />
                    <InputError class="mt-2" :message="form.errors.address" />
                </div>

            </FormModal>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import {ref, watch, reactive, computed} from 'vue';
import {router, useForm} from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Pagination from "@/Components/Pagination.vue";
import DataTable from "@/Components/Table/DataTable.vue";
import SortableHeader from "@/Components/Table/SortableHeader.vue";
import FormModal from "@/Components/FormModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import InputError from "@/Components/InputError.vue";
const props = defineProps({
    feds: Object,
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
    router.get('/federations', {
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
    date_of_foundation: '',
    address: '',
    logo: ''

});

// 2. Modal State Management
const isModalOpen = ref(false);
const editingModel = ref(null); // Will hold the item we are editing, or null if creating

// 3. Computed Properties for the Modal
const isEditing = computed(() => editingModel.value !== null);

const actionUrl = computed(() => {
    // Ensure you have these routes defined in web.php!
    return isEditing.value
        ? `/federations/${editingModel.value.id}` // PUT route
        : `/federations`;                         // POST route
});

// 4. Modal Triggers
const openCreateModal = () => {
    editingModel.value = null; // Set to Create mode
    form.reset();              // Clear inputs
    form.clearErrors();        // Clear old validation errors
    isModalOpen.value = true;
};

const openEditModal = (fed) => {
    editingModel.value = fed;  // Set to Edit mode
    form.name = fed.name;      // Populate the form with existing data!
    form.address = fed.address;
    form.date_of_foundation = fed.date_of_foundation
    form.logo = fed.logo
    form.clearErrors();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    setTimeout(() => form.reset(), 300); // Reset form after transition ends
};
</script>

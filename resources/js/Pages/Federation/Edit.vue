<script setup>
    import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
    import { Head } from '@inertiajs/vue3'
    import TextInput from "@/Components/TextInput.vue";
    import InputError from "@/Components/InputError.vue";
    import InputLabel from "@/Components/InputLabel.vue";
  defineProps({
      federation: Object,
  })
</script>

<template>
  <AuthenticatedLayout>
    <Head title="Edit" />
        <form>
            <div >
                <input-label for="name" name="name">Name</input-label>
                <text-input name="name" :model-value="federation.name" />
                <input-error for="name"/>
            </div>

        </form>
  </AuthenticatedLayout>
</template>

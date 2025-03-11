<script setup>
import { computed } from "vue";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import VueMultiselect from "vue-multiselect";

const props = defineProps({
  role: {
    type: Object,
    required: true,
  },
  permissions: {
    type: Array,
    required: true,
  },
});

const form = useForm({
  name: props.role.name,
  permissions: [],
});

const numberedPermissions = computed(() => {
  return props.permissions.map((permission, index) => ({
    ...permission,
    displayName: `${index + 1}. ${permission.name}`,
  }));
});
</script>

<template>

  <Head title="Update role" />

  <AuthenticatedLayout>
 


    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          แก้ไขข้อมูล</div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex justify-end space-x-4">
          <Link :href="route('roles.index')" class="custom-button-danger px-4">กลับ</Link>

        </div>
      </div>
    </div>
    <div class="max-w-7xl mx-auto">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8  bg-white  sm:rounded-xl shadow-md py-8 flex flex-col items-start">
        <form @submit.prevent="form.put(route('roles.update', role.id))" class="w-full flex flex-col items-start">
          <div class="mb-4 w-full text-left">
            <InputLabel for="name" value="Name" />
            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" autofocus autocomplete="username" />
            <InputError class="mt-2" :message="form.errors.name" />
          </div>
          <div class="mb-4 w-full text-left">
            <InputLabel for="permissions" value="Permissions" />
            <VueMultiselect v-model="form.permissions" :options="numberedPermissions" :multiple="true" :close-on-select="true"
              placeholder="Pick some" label="displayName" track-by="id" />
          </div>
          <div class="w-full flex justify-end">
            <Link  :href="route('roles.index')"  class="custom-button-danger py-5 mr-4" :class="{ 'opacity-25': form.processing }">ยกเลิก</Link>
            <PrimaryButton   class="custom-button-success py-5 ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">บันทึก</PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<style src="vue-multiselect/dist/vue-multiselect.css"></style>

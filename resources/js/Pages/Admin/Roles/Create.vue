<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import VueMultiselect from "vue-multiselect"; 

defineProps({
  permissions: Array,
});

const form = useForm({
  name: "",
  permissions: [],
});

const handleSubmit = () => {
  form.post(route('roles.store'));
};
</script>


<template>

  <Head title="Create new role" />

  <AuthenticatedLayout>
 
    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          เพิ่มหน้าที่
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex justify-end space-x-4">
          <Link :href="route('roles.index')" class="custom-button-danger px-4">กลับ</Link>

        </div>
      </div>
    </div>

    <div class="max-w-7xl mx-auto">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col items-start">
        <form @submit.prevent="form.post(route('roles.store'))" class="w-full flex flex-col items-start">
          <div class="mb-4 w-full text-left">

            <InputLabel for="name" value="Name" />
            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" autofocus
              autocomplete="username" />
            <InputError class="mt-2" :message="form.errors.name" />
          </div>
          <div class="mb-4 w-full text-left">
            <InputLabel for="permissions" value="Permissions" />
            <VueMultiselect v-model="form.permissions" :options="permissions" :multiple="true" :close-on-select="true"
              placeholder="Pick some" label="name" track-by="id" />
          </div>                   
           <div class="w-full flex justify-end">
            <a class="custom-button-danger py-5 mr-4" :class="{ 'opacity-25': form.processing }" :href="route('roles.index')">ยกเลิก</a>
            <a class="custom-button-success py-5 ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="handleSubmit">บันทึก</a>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>

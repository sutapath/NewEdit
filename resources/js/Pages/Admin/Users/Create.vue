<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import VueMultiselect from "vue-multiselect";

defineProps({
  roles: Array,
  permissions: Array
})

const form = useForm({
  name: "",
  email: "",
  password: "",
  password_confirmation: "",
  roles: [],
  // permissions: []
});

const submit = () => {
  form.post(route("users.store"), {
    onFinish: () => form.reset("password", "password_confirmation"),
  });
};
</script>
<template>
  <AuthenticatedLayout>
    <Head title="Create user" />
  <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          สร้างผู้ใช้งานใหม่
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex justify-end space-x-4">
          <Link :href="route('users.index')" 
                class="custom-button-danger">
                  กลับ
                </Link>
                <PrimaryButton class="custom-button-success" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                  บันทึก
                </PrimaryButton>
        </div>
      </div>
    </div>




    <div class="max-w-7xl mx-auto">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="submit" class="grid grid-cols-1 md:grid-cols-2 gap-6 w-full">
          <div class="mb-4">
            <InputLabel for="name" value="Name" />
            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
              autocomplete="name" />
            <InputError class="mt-2" :message="form.errors.name" />
          </div>
          <div class="mb-4">
            <InputLabel for="email" value="Email" />
            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
              autocomplete="username" />
            <InputError class="mt-2" :message="form.errors.email" />
          </div>
          <div class="mb-4">
            <InputLabel for="password" value="Password" />
            <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required
              autocomplete="new-password" />
            <InputError class="mt-2" :message="form.errors.password" />
          </div>
          <div class="mb-4">
            <InputLabel for="password_confirmation" value="Confirm Password" />
            <TextInput id="password_confirmation" type="password" class="mt-1 block w-full"
              v-model="form.password_confirmation" required autocomplete="new-password" />
            <InputError class="mt-2" :message="form.errors.password_confirmation" />
          </div>
          <div class="mb-4 ">
            <InputLabel for="roles" value="Roles" />
            <VueMultiselect v-model="form.roles" :options="roles" :multiple="true" :close-on-select="true"
              placeholder="Pick some" label="name" track-by="id" />
          </div>
          <!-- <div class="mb-4 ">
            <InputLabel for="permissions" value="Permissions" />
            <VueMultiselect v-model="form.permissions" :options="permissions" :multiple="true" :close-on-select="true"
              placeholder="Pick some" label="name" track-by="id" />
          </div> -->

        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
<style src="vue-multiselect/dist/vue-multiselect.css"></style>

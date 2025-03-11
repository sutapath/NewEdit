<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import { ref } from "vue";


defineProps(["permissions"]);

const form = useForm({
  name: ""
});

const showConfirmDeletePermissionModal = ref(false)

const confirmDeletePermission = () => {
  showConfirmDeletePermissionModal.value = true;
}

const closeModal = () => {
  showConfirmDeletePermissionModal.value = false;
}

const deletePermission = (id) => {
  form.delete(route('permissions.destroy', id), {
    onSuccess: () => closeModal()
  });
}
</script>

<template>

  <Head title="Update permission" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-4">
      <div class="py-2">
        <div class="max-w-7xl mx-auto py-2">
          <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
              <div
                class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-500 dark:border-gray-600">
                <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
                  <div class="px-4">Edit new role</div>
                  <Link :href="route('permissions.index')" class="custom-button-danger px-4">กลับ</Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="max-w-7xl mx-auto">
        <div
          class="max-w-6xl mx-auto sm:px-6 lg:px-8  bg-white  sm:rounded-xl shadow-md py-8 flex flex-col items-start">
          <form @submit.prevent="form.put(route('permissions.update', permission.id))"
            class="w-full flex flex-col items-start">
            <div class="mb-4 w-full text-left">
              <InputLabel for="name" value="Name" />
              <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" autofocus
                autocomplete="username" />
              <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="w-full flex justify-end">
              <Link :href="route('permissions.create')" class="custom-button-danger py-5 mr-4"
                :class="{ 'opacity-25': form.processing }">ยกเลิก</Link>
              <a class="custom-button-success py-5 ml-4" :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing" @click="handleSubmit">บันทึก</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

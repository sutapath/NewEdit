<!-- Template -->
<template>
    <Head title="Create new permission" />

    <AuthenticatedLayout>
        <div class="py-2">
            <div class="max-w-7xl mx-auto py-2">
                <div class="py-6">
                    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                        <div class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-500 dark:border-gray-600">
                            <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
                                <div class="px-4">Create new permission</div>
                                <Link :href="route('permissions.index')" class="custom-button-danger px-4">กลับ</Link>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex flex-col items-start">
                <form @submit.prevent="handleSubmit" class="w-full flex flex-col items-start">
                    <div class="mb-4 w-full text-left">
                        <InputLabel for="name" value="Name" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" autofocus autocomplete="username" />
                        <InputError class="mt-2" :message="form.errors.name" />
                    </div>
                    <div class="w-full flex justify-end">
                        <Link href="#" class="custom-button-danger py-5 mr-4" :class="{ 'opacity-25': form.processing }" :href="route('permissions.index')">ยกเลิก</Link>
                        <a href="#" class="custom-button-success py-5 ml-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="handleSubmit">บันทึก</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <Table>
  <template #header>
    <TableRow>
      <TableHeaderCell>ลำดับ</TableHeaderCell>
      <TableHeaderCell>ชื่อ</TableHeaderCell>
      <TableHeaderCell>การดำเนินงาน</TableHeaderCell>
    </TableRow>
  </template>
  <template #default>
    <TableRow
      v-for="(permission, index) in permissions"
      :key="permission.id"
      class="border-b"
    >
      <TableDataCell>{{ index + 1 }}</TableDataCell>
      <TableDataCell>{{ permission.name }}</TableDataCell>
      <TableDataCell class="space-x-4">
        <Link
          :href="route('permissions.edit', permission.id)"
          class="text-green-400 hover:text-green-600"
          >แก้ไข</Link
        >
        <button @click="confirmDeletePermission" class="text-red-400 hover:text-red-600">ลบ</button>
        <Modal :show="showConfirmDeletePermissionModal" @close="closeModal">
          <div class="p-6">
            <h2 class="text-lg font-semibold text-slate-800">ยืนยันการลบ Permission?</h2>
            <div class="mt-6 flex space-x-4">
              <DangerButton @click="deletePermission(permission.id)" class="custom-button-danger py-5 mr-4">ลบ</DangerButton>
              <SecondaryButton @click="closeModal" class="custom-button-success py-5 ml-4">ยกเลิก</SecondaryButton>
            </div>
          </div>
        </Modal>
      </TableDataCell>
    </TableRow>
  </template>
</Table>

      </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import InputLabel from "@/Components/InputLabel.vue";
import InputError from "@/Components/InputError.vue";
import TextInput from "@/Components/TextInput.vue";
import Modal from "@/Components/Modal.vue";
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

const showConfirmDeletePermissionModal = ref(false);

const openModal = () => {
    showConfirmDeletePermissionModal.value = true;
};

const closeModal = () => {
    showConfirmDeletePermissionModal.value = false;
};

const confirmDeletePermission = (id) => {
    showConfirmDeletePermissionModal.value = true;
};

const deletePermission = (id) => {
    form.delete(route('permissions.destroy', id), {
        onSuccess: () => closeModal()
    });
};

const handleSubmit = () => {
    form.post(route('permissions.store'), {
        onSuccess: () => {
            // Reset form fields or handle success message
            form.reset();
            closeModal();
        },
        onError: () => {
            // Handle errors, if any
        }
    });
};
</script>
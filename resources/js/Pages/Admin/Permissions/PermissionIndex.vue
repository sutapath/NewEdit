<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import Modal from "@/Components/Modal.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";

defineProps(["permissions"]);

const form = useForm({})

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
  <Head title="Permissions" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-4">
      <!-- Content -->
      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-gray-700 dark:bg-gray-800 overflow-hidden shadow-lg sm:rounded-lg border border-gray-500 dark:border-gray-600">
          <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
            <div class="px-4">Permission Index Page</div>
            <Link :href="route('permissions.create')" class="custom-button-success px-4">เพิ่ม</Link>
          </div>
        </div>
      </div>
      <div class="max-w-6xl mx-auto py-6 sm:px-6 lg:px-8  bg-white sm:rounded-xl">
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
              v-for="permission in permissions"
              :key="permission.id"
              class="border-b"
            >
              <TableDataCell>{{ permission.id }}</TableDataCell>
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
                    <DangerButton @click="deletePermission(permission.id)" class="custom-button-danger py-5 mr-4">ลบ
                    </DangerButton>
                    <SecondaryButton @click="closeModal" class="custom-button-success py-5 ml-4">ยกเลิก
                    </SecondaryButton>
                  </div>
                 </div>
               </Modal>
              </TableDataCell>
            </TableRow>
          </template>
        </Table>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import Table from '@/Components/Table.vue';
import TableRow from '@/Components/TableRow.vue';
import TableHeaderCell from '@/Components/TableHeaderCell.vue';
import TableDataCell from '@/Components/TableDataCell.vue';
import Modal from '@/Components/Modal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
const props = defineProps(['roles']);

const form = useForm({});
const showConfirmDeleteRoleModal = ref(false);
const roleIdToDelete = ref(null);

const confirmDeleteRole = (id) => {
  roleIdToDelete.value = id;
  showConfirmDeleteRoleModal.value = true;
};

const closeModal = () => {
  showConfirmDeleteRoleModal.value = false;
  roleIdToDelete.value = null;
};

const deleteRole = () => {
  form.delete(route('roles.destroy', roleIdToDelete.value), {
    onSuccess: () => closeModal(),
  });
};

// Debugging
console.log('Roles:', props.roles);
</script>

<template>

  <Head title="Dashboard" />
  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          บทบาทหน้าที่
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300" />
        </div>
        <div class="flex justify-end space-x-4">
          <Link :href="route('roles.create')" class="custom-button-success px-4">เพิ่ม</Link>

        </div>
      </div>
    </div>
    <div class="max-w-7xl mx-auto py-4 mt-3">



      <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 bg-white sm:rounded-xl">
        <Table>
          <template #header>
            <TableRow>
              <TableHeaderCell>ลำดับ</TableHeaderCell>
              <TableHeaderCell>ชื่อ</TableHeaderCell>
              <TableHeaderCell>สิทธิ์การเข้าถึง</TableHeaderCell>
              <TableHeaderCell>การดำเนินงาน</TableHeaderCell>
            </TableRow>
          </template>
          <template #default>
            <TableRow v-for="role in props.roles" :key="role.id" class="border-b">
              <TableDataCell>{{ role.id }}</TableDataCell>
              <TableDataCell>{{ role.name }}</TableDataCell>
              <TableDataCell>
                <ul>
                  <li v-for="permission in role.permissions" :key="permission.id">
                    {{ permission.name }}
                  </li>
                </ul>
              </TableDataCell>
              <TableDataCell class="space-x-4">
                <Link :href="route('roles.edit', role.id)" class="custom-button-warning">แก้ไข</Link>
                <DangerButton @click="confirmDeleteRole(role.id)" class="custom-button-danger">ลบ</DangerButton>
                <Modal :show="showConfirmDeleteRoleModal" @close="closeModal">
                  <div class="p-6">
                    <h2 class="text-lg font-semibold text-slate-800">ยืนยันการลบ Role?</h2>
                    <div class="mt-6 flex space-x-4">
                      <DangerButton @click="deleteRole">ลบ</DangerButton>
                      <SecondaryButton @click="closeModal">ยกเลิก</SecondaryButton>
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

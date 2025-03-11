<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
// สร้างรายการหลาย citizen_id
const form = useForm({
  students: [
    { citizen_id: '' }, // รายการแรก
  ],
});

// เพิ่มแถวใหม่สำหรับการกรอกข้อมูล
function addRow() {
  form.students.push({ citizen_id: '' });
}

// ลบแถวข้อมูล
function removeRow(index) {
  if (form.students.length > 1) {
    form.students.splice(index, 1);
  }
}

// ส่งฟอร์ม
function submit() {
  form.post(route('students.store'), {
    onSuccess: () => form.reset(), // Reset the form on success
  });
}
</script>

<template>

  <Head title="Create Student" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto    py-4 mt-20 px-10">

      <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class=" py-2   text-gray-800 rounded-t-lg text-xl font-bold">
          เพิ่มผู้ใช้งาน
        </div>
        <div class="flex-1 mr-3 ml-3 ">
          <hr class="border-t border-gray-300">
        </div>
        <Link :href="route('users.index')" 
                class="custom-button-danger">
                  กลับ
                </Link>
      </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl mb-20">
      <form @submit.prevent="submit">
        <div class="p-6 space-y-4">
          <!-- เริ่มต้นตาราง -->
          <Table class="w-full overflow-x-auto">
        <template #header>
          <TableRow>
            <TableHeaderCell>หมายเลขบัตรประชาชน</TableHeaderCell>
            <TableHeaderCell>เพิ่มเติม</TableHeaderCell>
          </TableRow>
        </template>
        <template #default>
              <!-- Loop รายการ students ที่มี -->
              <TableRow v-for="(student, index) in form.students" :key="index" class="border-b">
                <TableDataCell class="px-4 py-2">
                  <TextInput :id="'citizen_id_' + index" v-model="student.citizen_id" type="text"
                    placeholder="กรอกหมายเลขบัตรประชาชน" class="mt-1 block w-full" />
                  <span v-if="form.errors[`students.${index}.citizen_id`]" class="text-red-500 text-sm">
                    {{ form.errors[`students.${index}.citizen_id`] }}
                  </span>
                </TableDataCell>
                <TableDataCell class="px-4 py-2">
                  <button type="button" @click="removeRow(index)" class="text-red-600">ลบ</button>
                </TableDataCell>
              </TableRow>
            </template>
          </Table>
          <!-- ปุ่มเพิ่มแถว -->
          <div class="flex justify-between items-center mt-4">

            <button type="button" @click="addRow" class="text-blue-600">เพิ่มแถวใหม่</button>
          </div>

          <div class="flex justify-end space-x-4 mt-4">
  <PrimaryButton class="custom-button-success px-1 py-1" :class="{ 'opacity-25': form.processing }"
    :disabled="form.processing">
    บันทึก
  </PrimaryButton>

  <Link :href="route('students.index')" class="custom-button-danger mr-3 px-1 py-1">กลับ</Link>
</div>

        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

<style scoped>
/* Customize styles as needed */
</style>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import Table from "@/Components/Table.vue";
import TableRow from "@/Components/TableRow.vue";
import TableHeaderCell from "@/Components/TableHeaderCell.vue";
import TableDataCell from "@/Components/TableDataCell.vue";
import Swal from 'sweetalert2';

// รายการ role ที่ให้เลือก
const roles = [
  { value: '', label: 'เลือกบทบาท' },
  { value: 'admin', label: 'Admin' },
  { value: 'officer', label: 'Officer' },
  { value: 'scholar', label: 'Scholar' },
  { value: 'student', label: 'Student' },
  { value: 'member', label: 'Member' },
  { value: 'intlscholar', label: 'IntlScholar' },
  { value: 'manager', label: 'Manager' }
];

// กำหนดค่าฟอร์ม
const form = useForm({
  role: '',
  members: [{ citizen_id: '', title: '', fname: '', lname: '', code: '' }],
});

// ฟังก์ชันเพิ่มแถว
function addRow() {
  form.members.push({ citizen_id: '', title: '', fname: '', lname: '', code: '' });
}

// ฟังก์ชันลบแถว
function removeRow(index) {
  Swal.fire({
    title: 'คุณแน่ใจหรือไม่?',
    text: 'คุณต้องการลบแถวนี้หรือไม่!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ลบ',
    cancelButtonText: 'ยกเลิก',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
  }).then((result) => {
    if (result.isConfirmed) {
      form.members.splice(index, 1);
    }
  });
}

// ฟังก์ชันยกเลิก
function confirmCancel() {
  Swal.fire({
    title: 'คุณแน่ใจหรือไม่?',
    text: 'หากคุณยกเลิก ข้อมูลที่กรอกจะหายไป!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ยืนยันยกเลิก',
    cancelButtonText: 'ยกเลิก',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
  }).then((result) => {
    if (result.isConfirmed) {
      window.location.href = route('users.index');
    }
  });
} 
// ฟังก์ชันส่งฟอร์ม
function submit() {
  form.post(route('members.store'), {
    onSuccess: () => {
      Swal.fire({
        title: 'สำเร็จ!',
        text: 'เพิ่มข้อมูลเรียบร้อยแล้ว',
        icon: 'success',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#3085d6', // กำหนดสีปุ่ม (เปลี่ยนเป็นสีที่ต้องการ)
      }).then(() => {
        form.reset();
      });
    },
    onError: () => {
      Swal.fire({
        title: 'เกิดข้อผิดพลาด!',
        text: 'ไม่สามารถเพิ่มข้อมูลได้',
        icon: 'error',
        confirmButtonText: 'ตกลง',
        confirmButtonColor: '#d33', // สีแดง
      });

    }
  });
}
</script>

<template>

  <Head title="Create officer" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          เพิ่มเจ้าหน้าที่
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300">
        </div>
        <Link :href="route('users.index')" class="custom-button-danger">
        กลับ
        </Link>
      </div>
    </div>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl mb-20 p-6">
      <form @submit.prevent="submit">
        <div class="mb-4">
          <label for="role" class="block text-gray-700 font-medium">เลือกบทบาท</label>
          <select v-model="form.role" id="role" class="border-gray-300 rounded w-full p-2">
            <option v-for="role in roles" :key="role.value" :value="role.value" :disabled="role.value === ''">
              {{ role.label }}
            </option>
          </select>
        </div>

        <Table class="w-full overflow-x-auto">
          <template #header>
            <TableRow>
              <TableHeaderCell>ลำดับที่</TableHeaderCell>
              <TableHeaderCell>หมายเลขบัตรประชาชน</TableHeaderCell>
              <TableHeaderCell>คำนำหน้า</TableHeaderCell>
              <TableHeaderCell>ชื่อ</TableHeaderCell>
              <TableHeaderCell>นามสกุล</TableHeaderCell>
              <TableHeaderCell>รหัส</TableHeaderCell>
              <TableHeaderCell>จัดการ</TableHeaderCell>
            </TableRow>
          </template>

          <template #default>
            <TableRow v-for="(officer, index) in form.members" :key="index" class="border-b">
              <TableDataCell>{{ index + 1 }}</TableDataCell> <!-- ลำดับที่ -->
              <TableDataCell>
                <TextInput v-model="officer.citizen_id" type="text" placeholder="กรอกหมายเลขบัตรประชาชน" />
              </TableDataCell>
              <TableDataCell>
                <select v-model="officer.title">
                  <option value="">เลือก</option>
                  <option value="0">นาย</option>
                  <option value="1">นาง</option>
                  <option value="2">นางสาว</option>
                </select>
              </TableDataCell>
              <TableDataCell>
                <TextInput v-model="officer.fname" type="text" placeholder="ชื่อ" />
              </TableDataCell>
              <TableDataCell>
                <TextInput v-model="officer.lname" type="text" placeholder="นามสกุล" />
              </TableDataCell>
              <TableDataCell>
                <TextInput v-model="officer.code" type="text" placeholder="รหัส" />
              </TableDataCell>
              <TableDataCell>
                <button type="button" @click="removeRow(index)" class="custom-button-danger">ลบ</button>
              </TableDataCell>
            </TableRow>
          </template>
        </Table>

        <div class="flex justify-between mt-4">
          <button type="button" @click="addRow" class="text-blue-600">เพิ่มแถวใหม่</button>
          <PrimaryButton class="custom-button-success">บันทึก</PrimaryButton>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

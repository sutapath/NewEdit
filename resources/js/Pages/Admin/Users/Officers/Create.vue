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

// สร้างรายการ officers ที่มีข้อมูลหลายฟิลด์
const form = useForm({
  officers: [
    { citizen_id: '', title: '', fname: '', lname: '', code: '' }, // รายการแรก
  ],
});

// เพิ่มแถวใหม่
function addRow() {
  form.officers.push({ citizen_id: '', title: '', fname: '', lname: '', code: '' });
}

// ลบแถว
function removeRow(index) {
  Swal.fire({
    title: 'คุณแน่ใจหรือไม่?',
    text: 'คุณต้องการลบแถวนี้หรือไม่!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ลบ',
    cancelButtonText: 'ยกเลิก',
    confirmButtonColor: '#d33',  // สีแดงสำหรับปุ่ม 'ลบ'
    cancelButtonColor: '#3085d6',  // สีน้ำเงินสำหรับปุ่ม 'ยกเลิก'
  }).then((result) => {
    if (result.isConfirmed) {
      form.officers.splice(index, 1);
    }
  });
}

// ยืนยันการยกเลิก
function confirmCancel() {
  Swal.fire({
    title: 'คุณแน่ใจหรือไม่?',
    text: 'หากคุณยกเลิก ข้อมูลที่กรอกจะหายไป!',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ยืนยันยกเลิก',
    cancelButtonText: 'ยกเลิก',
    confirmButtonColor: '#d33', // สีแดงสำหรับปุ่ม 'ยืนยันยกเลิก'
    cancelButtonColor: '#3085d6', // สีน้ำเงินสำหรับปุ่ม 'ยกเลิก'
  }).then((result) => {
    if (result.isConfirmed) {
      // ไปที่หน้าก่อนหน้านี้หรือล้างข้อมูลที่กรอก
      window.location.href = route('officers.index');
    }
  });
}

// ส่งฟอร์ม
function submit() {
  form.post(route('officers.store'), {
    onSuccess: () => {
      Swal.fire({
        title: 'สำเร็จ!',
        text: 'เพิ่มข้อมูลเจ้าหน้าที่เรียบร้อยแล้ว',
        icon: 'success',
        confirmButtonText: 'ตกลง',
        customClass: {
          confirmButton: 'bg-green-500 text-white hover:bg-green-600' // ปุ่มตกลง (สีเขียว)
        }
      }).then(() => {
        form.reset();
      });
    },
    onError: () => {
      Swal.fire({
        title: 'เกิดข้อผิดพลาด!',
        text: 'ไม่สามารถเพิ่มข้อมูลเจ้าหน้าที่ได้',
        icon: 'error',
        confirmButtonText: 'ตกลง',
        customClass: {
          confirmButton: 'bg-red-500 text-white hover:bg-red-600' // ปุ่มตกลง (สีแดง)
        }
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

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 bg-white sm:rounded-xl mb-20">
      <form @submit.prevent="submit">
        <div class="p-6 space-y-4">
          <!-- ตารางข้อมูล -->
          <Table class="w-full overflow-x-auto">
            <template #header>
              <TableRow>
                <TableHeaderCell>หมายเลขบัตรประชาชน</TableHeaderCell>
                <TableHeaderCell>คำนำหน้า</TableHeaderCell>
                <TableHeaderCell>ชื่อ</TableHeaderCell>
                <TableHeaderCell>นามสกุล</TableHeaderCell>
                <TableHeaderCell>รหัส</TableHeaderCell>
                <TableHeaderCell>เพิ่มเติม</TableHeaderCell>
              </TableRow>
            </template>
            <template #default>
              <TableRow v-for="(officer, index) in form.officers" :key="index" class="border-b">
                <!-- หมายเลขบัตรประชาชน -->
                <TableDataCell class="px-4 py-2">
                  <TextInput v-model="officer.citizen_id" type="text" placeholder="กรอกหมายเลขบัตรประชาชน"
                    class="mt-1 block w-full" />
                  <span v-if="form.errors[`officers.${index}.citizen_id`]" class="text-red-500 text-sm">
                    {{ form.errors[`officers.${index}.citizen_id`] }}
                  </span>
                </TableDataCell>

                <!-- คำนำหน้า -->
                <TableDataCell class="px-4 py-2">
                  <select v-model="officer.title" class="border-gray-300 rounded w-full p-2">
                    <option value="">เลือก</option>
                    <option value="0">นาย</option>
                    <option value="1">นาง</option>
                    <option value="2">นางสาว</option>
                  </select>
                  <span v-if="form.errors[`officers.${index}.title`]" class="text-red-500 text-sm">
                    {{ form.errors[`officers.${index}.title`] }}
                  </span>
                </TableDataCell>

                <!-- ชื่อ -->
                <TableDataCell class="px-4 py-2">
                  <TextInput v-model="officer.fname" type="text" placeholder="ชื่อ" class="mt-1 block w-full" />
                  <span v-if="form.errors[`officers.${index}.fname`]" class="text-red-500 text-sm">
                    {{ form.errors[`officers.${index}.fname`] }}
                  </span>
                </TableDataCell>

                <!-- นามสกุล -->
                <TableDataCell class="px-4 py-2">
                  <TextInput v-model="officer.lname" type="text" placeholder="นามสกุล" class="mt-1 block w-full" />
                  <span v-if="form.errors[`officers.${index}.lname`]" class="text-red-500 text-sm">
                    {{ form.errors[`officers.${index}.lname`] }}
                  </span>
                </TableDataCell>

                <!-- รหัส -->
                <TableDataCell class="px-4 py-2">
                  <TextInput v-model="officer.code" type="text" placeholder="รหัสเจ้าหน้าที่"
                    class="mt-1 block w-full" />
                  <span v-if="form.errors[`officers.${index}.code`]" class="text-red-500 text-sm">
                    {{ form.errors[`officers.${index}.code`] }}
                  </span>
                </TableDataCell>

                <!-- ปุ่มลบ -->
                <TableDataCell class="px-4 py-2">
                  <button type="button" @click="removeRow(index)" class="custom-button-danger">ลบ</button>
                </TableDataCell>
              </TableRow>
            </template>
          </Table>

          <div class="flex justify-between items-center mt-4">
            <button type="button" @click="addRow" class="text-blue-600">เพิ่มแถวใหม่</button>
            <div class="flex space-x-4">
              <PrimaryButton class="custom-button-success px-4 py-2">บันทึก</PrimaryButton>
              <button type="button" @click="confirmCancel" class="custom-button-danger px-4 py-2">ยกเลิก</button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </AuthenticatedLayout>
</template>

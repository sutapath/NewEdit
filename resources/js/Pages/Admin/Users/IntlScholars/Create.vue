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

// สร้างรายการ intlScholars ที่มีข้อมูลหลายฟิลด์
const form = useForm({
  intlScholars: [
    { citizen_id: '', title: '', fname: '', lname: '', code: '' }, // รายการแรก
  ],
}); 
function addRow() {
  form.intlScholars.push({ citizen_id: '', title: '', fname: '', lname: '', code: '' });
}
 
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
      form.intlScholars.splice(index, 1);
    }
  });
}
 
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
      window.location.href = route('intlScholars.index');
    }
  });
}

function submit() {
  form.post(route('intlScholars.store'), {
    onSuccess: () => {
      Swal.fire({
        title: 'สำเร็จ!',
        text: 'เพิ่มข้อมูลนักศึกษาต่างชาติเรียบร้อยแล้ว',
        icon: 'success',
        confirmButtonText: 'ตกลง',
        customClass: {
          confirmButton: 'bg-green-500 text-white hover:bg-green-600'
        }
      }).then(() => {
        form.reset();
      });
    },
    onError: (errors) => {
      console.log("Form Submission Errors:", errors);

      Swal.fire({
        title: 'เกิดข้อผิดพลาด!',
        text: 'ไม่สามารถเพิ่มข้อมูลนักศึกษาต่างชาติได้',
        icon: 'error',
        confirmButtonText: 'ตกลง',
        customClass: {
          confirmButton: 'bg-red-500 text-white hover:bg-red-600'
        }
      });
    }
  });
}


</script>

<template>

  <Head title="Create intlScholar" />

  <AuthenticatedLayout>
    <div class="max-w-7xl mx-auto py-4 mt-20 px-10">
      <div class="py-2 px-4 text-white dark:text-gray-100 flex justify-between items-center">
        <div class="py-2 text-gray-800 rounded-t-lg text-xl font-bold">
          เพิ่มนักศึกษาต่างชาติ
        </div>
        <div class="flex-1 mr-3 ml-3">
          <hr class="border-t border-gray-300">
        </div>
        <Link :href="route('intlScholars.index')" class="custom-button-danger">
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
                <TableHeaderCell>รหัสนักศึกษา</TableHeaderCell>
                <TableHeaderCell>เพิ่มเติม</TableHeaderCell>
              </TableRow>
            </template>
            <template #default>
              <TableRow v-for="(student, index) in form.intlScholars" :key="index" class="border-b">
                <!-- หมายเลขบัตรประชาชน -->
                <TableDataCell class="px-4 py-2">
                  <TextInput v-model="student.citizen_id" type="text" placeholder="กรอกหมายเลขบัตรประชาชน"
                    class="mt-1 block w-full" />
                  <span v-if="form.errors[`intlScholars.${index}.citizen_id`]" class="text-red-500 text-sm">
                    {{ form.errors[`intlScholars.${index}.citizen_id`] }}
                  </span>
                </TableDataCell>

                <!-- คำนำหน้า -->
                <TableDataCell class="px-4 py-2">
                  <select v-model="student.title" class="border-gray-300 rounded w-full p-2">
                    <option value="">เลือก</option>
                    <option value="0">นาย</option>
                    <option value="1">นาง</option>
                    <option value="2">นางสาว</option>
                  </select>
                  <span v-if="form.errors[`intlScholars.${index}.title`]" class="text-red-500 text-sm">
                    {{ form.errors[`intlScholars.${index}.title`] }}
                  </span>
                </TableDataCell>

                <!-- ชื่อ -->
                <TableDataCell class="px-4 py-2">
                  <TextInput v-model="student.fname" type="text" placeholder="ชื่อ" class="mt-1 block w-full" />
                  <span v-if="form.errors[`intlScholars.${index}.fname`]" class="text-red-500 text-sm">
                    {{ form.errors[`intlScholars.${index}.fname`] }}
                  </span>
                </TableDataCell>

                <!-- นามสกุล -->
                <TableDataCell class="px-4 py-2">
                  <TextInput v-model="student.lname" type="text" placeholder="นามสกุล" class="mt-1 block w-full" />
                  <span v-if="form.errors[`intlScholars.${index}.lname`]" class="text-red-500 text-sm">
                    {{ form.errors[`intlScholars.${index}.lname`] }}
                  </span>
                </TableDataCell>

                <!-- รหัสนักศึกษา -->
                <TableDataCell class="px-4 py-2">
                  <TextInput v-model="student.code" type="text" placeholder="รหัสนักศึกษา"
                    class="mt-1 block w-full" />
                  <span v-if="form.errors[`intlScholars.${index}.code`]" class="text-red-500 text-sm">
                    {{ form.errors[`intlScholars.${index}.code`] }}
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
